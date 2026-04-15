<?php

namespace App\Core;

use PDO;

/**
 * VotingEngine - Core PHP voting logic
 * Handles vote validation, IP restriction, and audit tracking
 */
class VotingEngine
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Validate and process a vote
     * Core business logic for IP-restricted voting
     */
    public function castVote($pollId, $optionId, $ipAddress)
    {
        try {
            // Sanitize inputs
            $pollId = intval($pollId);
            $optionId = intval($optionId);
            $ipAddress = $this->sanitizeIp($ipAddress);

            // Validate poll exists and is active
            if (!$this->pollExists($pollId)) {
                return ['success' => false, 'message' => 'Poll not found'];
            }

            if (!$this->pollIsActive($pollId)) {
                return ['success' => false, 'message' => 'This poll is inactive'];
            }

            // Validate option belongs to poll
            if (!$this->optionBelongsToPoll($optionId, $pollId)) {
                return ['success' => false, 'message' => 'Invalid option for this poll'];
            }

            // Check IP restriction - only ONE active vote per IP per poll
            $existingVote = $this->getActiveVoteByIp($pollId, $ipAddress);

            if ($existingVote) {
                return ['success' => false, 'message' => 'IP has already voted on this poll'];
            }

            // Store the vote
            $voteId = $this->storeVote($pollId, $optionId, $ipAddress);

            if (!$voteId) {
                return ['success' => false, 'message' => 'Failed to record vote'];
            }

            // Log vote action
            $this->logVoteHistory($pollId, $optionId, $ipAddress, 'vote', 
                ['vote_id' => $voteId, 'timestamp' => date('Y-m-d H:i:s')]);

            return [
                'success' => true,
                'message' => 'Vote recorded successfully',
                'vote_id' => $voteId,
                'ip_address' => $ipAddress
            ];

        } catch (\Exception $e) {
            error_log('VotingEngine Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred'];
        }
    }

    /**
     * Release an IP's vote from a poll (Admin action)
     * Marks vote as inactive and logs the action
     */
    public function releaseVote($pollId, $ipAddress)
    {
        try {
            $pollId = intval($pollId);
            $ipAddress = $this->sanitizeIp($ipAddress);

            // Get current active vote
            $vote = $this->getActiveVoteByIp($pollId, $ipAddress);

            if (!$vote) {
                return ['success' => false, 'message' => 'No active vote found for this IP'];
            }

            // Mark vote as inactive
            $stmt = $this->pdo->prepare("
                UPDATE votes 
                SET is_active = FALSE, updated_at = NOW()
                WHERE id = ? AND poll_id = ?
            ");
            $stmt->execute([$vote['id'], $pollId]);

            // Log the release action
            $this->logVoteHistory($pollId, $vote['option_id'], $ipAddress, 'release',
                ['released_vote_id' => $vote['id'], 'original_option_id' => $vote['option_id']]);

            return [
                'success' => true,
                'message' => 'Vote released successfully',
                'ip_address' => $ipAddress,
                'previous_option' => $vote['option_id']
            ];

        } catch (\Exception $e) {
            error_log('VotingEngine Release Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to release vote'];
        }
    }

    /**
     * Handle revote after release
     * Called when IP votes again after their vote was released
     */
    public function reVote($pollId, $optionId, $ipAddress)
    {
        try {
            $pollId = intval($pollId);
            $optionId = intval($optionId);
            $ipAddress = $this->sanitizeIp($ipAddress);

            // Ensure no active vote exists
            $existingVote = $this->getActiveVoteByIp($pollId, $ipAddress);
            if ($existingVote) {
                return ['success' => false, 'message' => 'IP already has an active vote'];
            }

            // Store new vote
            $voteId = $this->storeVote($pollId, $optionId, $ipAddress);

            // Log revote action
            $this->logVoteHistory($pollId, $optionId, $ipAddress, 'revote',
                ['new_vote_id' => $voteId, 'new_option_id' => $optionId]);

            return [
                'success' => true,
                'message' => 'Revote recorded successfully',
                'vote_id' => $voteId
            ];

        } catch (\Exception $e) {
            error_log('VotingEngine ReVote Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to process revote'];
        }
    }

    /**
     * Get poll results with vote counts
     */
    public function getPollResults($pollId)
    {
        try {
            $pollId = intval($pollId);

            $stmt = $this->pdo->prepare("
                SELECT 
                    po.id,
                    po.option_text,
                    COUNT(CASE WHEN v.is_active = TRUE THEN 1 END) as vote_count,
                    (
                        SELECT COUNT(*) FROM votes 
                        WHERE poll_id = ? AND is_active = TRUE
                    ) as total_votes
                FROM poll_options po
                LEFT JOIN votes v ON po.id = v.option_id AND po.poll_id = v.poll_id
                WHERE po.poll_id = ?
                GROUP BY po.id, po.option_text
                ORDER BY po.id ASC
            ");
            $stmt->execute([$pollId, $pollId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totalVotes = 0;
            if (!empty($results)) {
                $totalVotes = intval($results[0]['total_votes']);
            }

            $formattedResults = [];
            foreach ($results as $row) {
                $percentage = $totalVotes > 0 ? round(($row['vote_count'] / $totalVotes) * 100, 2) : 0;
                $formattedResults[] = [
                    'id' => $row['id'],
                    'option_text' => $row['option_text'],
                    'vote_count' => intval($row['vote_count']),
                    'percentage' => $percentage
                ];
            }

            return [
                'success' => true,
                'total_votes' => $totalVotes,
                'options' => $formattedResults
            ];

        } catch (\Exception $e) {
            error_log('VotingEngine GetResults Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch results'];
        }
    }

    /**
     * Get vote history (audit trail)
     */
    public function getVoteHistory($pollId, $ipAddress = null)
    {
        try {
            $pollId = intval($pollId);

            if ($ipAddress) {
                $ipAddress = $this->sanitizeIp($ipAddress);
                $stmt = $this->pdo->prepare("
                    SELECT 
                        vh.id,
                        vh.poll_id,
                        vh.option_id,
                        po.option_text,
                        vh.ip_address,
                        vh.action_type,
                        vh.timestamp,
                        vh.details
                    FROM vote_history vh
                    LEFT JOIN poll_options po ON vh.option_id = po.id
                    WHERE vh.poll_id = ? AND vh.ip_address = ?
                    ORDER BY vh.timestamp DESC
                ");
                $stmt->execute([$pollId, $ipAddress]);
            } else {
                $stmt = $this->pdo->prepare("
                    SELECT 
                        vh.id,
                        vh.poll_id,
                        vh.option_id,
                        po.option_text,
                        vh.ip_address,
                        vh.action_type,
                        vh.timestamp,
                        vh.details
                    FROM vote_history vh
                    LEFT JOIN poll_options po ON vh.option_id = po.id
                    WHERE vh.poll_id = ?
                    ORDER BY vh.timestamp DESC
                ");
                $stmt->execute([$pollId]);
            }

            return [
                'success' => true,
                'history' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch (\Exception $e) {
            error_log('VotingEngine GetHistory Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch history'];
        }
    }

    /**
     * Get all unique IPs that voted on a poll
     */
    public function getVotersByPoll($pollId)
    {
        try {
            $pollId = intval($pollId);

            $stmt = $this->pdo->prepare("
                SELECT 
                    v.ip_address,
                    v.id as vote_id,
                    po.option_text,
                    v.voted_at,
                    v.is_active,
                    (
                        SELECT vh.action_type 
                        FROM vote_history vh 
                        WHERE vh.ip_address = v.ip_address 
                        AND vh.poll_id = ?
                        ORDER BY vh.timestamp DESC LIMIT 1
                    ) as last_action
                FROM votes v
                JOIN poll_options po ON v.option_id = po.id
                WHERE v.poll_id = ?
                GROUP BY v.ip_address
                ORDER BY v.voted_at DESC
            ");
            $stmt->execute([$pollId, $pollId]);

            return [
                'success' => true,
                'voters' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch (\Exception $e) {
            error_log('VotingEngine GetVoters Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch voters'];
        }
    }

    // ===== PRIVATE HELPER METHODS =====

    /**
     * Check if IP already has an active vote on this poll
     */
    private function getActiveVoteByIp($pollId, $ipAddress)
    {
        $stmt = $this->pdo->prepare("
            SELECT id, option_id, poll_id
            FROM votes
            WHERE poll_id = ? AND ip_address = ? AND is_active = TRUE
            LIMIT 1
        ");
        $stmt->execute([$pollId, $ipAddress]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Store a vote in database
     */
    private function storeVote($pollId, $optionId, $ipAddress)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO votes (poll_id, option_id, ip_address, voted_at, is_active)
            VALUES (?, ?, ?, NOW(), TRUE)
        ");
        $result = $stmt->execute([$pollId, $optionId, $ipAddress]);

        return $result ? $this->pdo->lastInsertId() : false;
    }

    /**
     * Log vote action to history table
     */
    private function logVoteHistory($pollId, $optionId, $ipAddress, $action, $details = [])
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO vote_history (poll_id, option_id, ip_address, action_type, timestamp, details)
            VALUES (?, ?, ?, ?, NOW(), ?)
        ");

        return $stmt->execute([
            $pollId,
            $optionId,
            $ipAddress,
            $action,
            json_encode($details)
        ]);
    }

    /**
     * Validate IP address format
     */
    private function sanitizeIp($ip)
    {
        // Validate IPv4 and IPv6
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
        throw new \Exception('Invalid IP address format');
    }

    /**
     * Check if poll exists
     */
    private function pollExists($pollId)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM polls WHERE id = ? LIMIT 1");
        $stmt->execute([$pollId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Check if poll is active
     */
    private function pollIsActive($pollId)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM polls WHERE id = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$pollId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Validate option belongs to poll
     */
    private function optionBelongsToPoll($optionId, $pollId)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM poll_options WHERE id = ? AND poll_id = ? LIMIT 1");
        $stmt->execute([$optionId, $pollId]);
        return $stmt->fetch() !== false;
    }
}
