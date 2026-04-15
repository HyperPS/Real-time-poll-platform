<?php

namespace App\Http\Controllers;

use App\Core\VotingEngine;

class VoteController
{
    private $votingEngine;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->votingEngine = new VotingEngine($pdo);
    }

    /**
     * Cast vote (AJAX endpoint)
     */
    public function castVote()
    {
        header('Content-Type: application/json');

        // Verify authentication
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        // Verify POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'message' => 'Method not allowed']);
        }

        // Get input
        $input = json_decode(file_get_contents('php://input'), true);
        $pollId = $input['poll_id'] ?? '';
        $optionId = $input['option_id'] ?? '';

        // Validate input
        if (empty($pollId) || empty($optionId)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Invalid input']);
        }

        // Get client IP
        $ipAddress = $this->getClientIp();
        $userId = $_SESSION['user_id'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Cast vote using VotingEngine
        $result = $this->votingEngine->castVote($pollId, $optionId, $ipAddress, $userId, $userAgent);

        // Log vote activity
        if ($result['success']) {
            log_activity($this->pdo, $userId, 'vote_cast', "Voted on poll {$pollId}, option {$optionId}", [
                'poll_id' => $pollId,
                'option_id' => $optionId,
                'vote_id' => $result['vote_id'] ?? null
            ]);
        }

        // Set response code
        http_response_code($result['success'] ? 200 : 400);

        return json_encode($result);
    }

    /**
     * Get poll results (AJAX endpoint)
     */
    public function getResults()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $pollId = $_GET['poll_id'] ?? '';

        if (empty($pollId)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Poll ID required']);
        }

        $result = $this->votingEngine->getPollResults(intval($pollId));
        http_response_code($result['success'] ? 200 : 400);

        return json_encode($result);
    }

    /**
     * Check if IP has voted on poll
     */
    public function checkVoteStatus()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $pollId = $_GET['poll_id'] ?? '';

        if (empty($pollId)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Poll ID required']);
        }

        $ipAddress = $this->getClientIp();
        $pollId = intval($pollId);

        // Check for active vote
        $stmt = $this->pdo->prepare("
            SELECT id, option_id FROM votes
            WHERE poll_id = ? AND ip_address = ? AND is_active = TRUE
            LIMIT 1
        ");
        $stmt->execute([$pollId, $ipAddress]);
        $vote = $stmt->fetch(\PDO::FETCH_ASSOC);

        http_response_code(200);
        return json_encode([
            'success' => true,
            'hasVoted' => $vote ? true : false,
            'optionId' => $vote ? intval($vote['option_id']) : null
        ]);
    }

    /**
     * Get client's real IP address
     */
    private function getClientIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Trim whitespace
        $ip = trim($ip);

        // Validate IP
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }

        return '127.0.0.1';
    }
}
