<?php

namespace App\Http\Controllers;

use App\Core\VotingEngine;
use App\Models\Poll;

class AdminController
{
    private $votingEngine;
    private $pollModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->votingEngine = new VotingEngine($pdo);
        $this->pollModel = new Poll($pdo);
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['error'] = 'Admin access only';
            header('Location: /dashboard');
            exit;
        }

        $polls = $this->pollModel->getAll();
        return ['view' => 'admin/dashboard', 'data' => ['polls' => $polls]];
    }

    /**
     * Poll management view
     */
    public function managePoll($pollId)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            header('Location: /');
            exit;
        }

        $pollId = intval($pollId);
        $poll = $this->pollModel->getWithOptions($pollId);

        if (!$poll) {
            $_SESSION['error'] = 'Poll not found';
            header('Location: /admin/dashboard');
            exit;
        }

        // Get voters
        $votersResult = $this->votingEngine->getVotersByPoll($pollId);
        $voters = $votersResult['voters'] ?? [];

        return [
            'view' => 'admin/manage-poll',
            'data' => [
                'poll' => $poll,
                'voters' => $voters
            ]
        ];
    }

    /**
     * Get voters for poll (AJAX)
     */
    public function getVoters()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $pollId = $_GET['poll_id'] ?? '';

        if (empty($pollId)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Poll ID required']);
        }

        $result = $this->votingEngine->getVotersByPoll(intval($pollId));
        http_response_code($result['success'] ? 200 : 400);

        return json_encode($result);
    }

    /**
     * Release IP vote (AJAX)
     */
    public function releaseVote()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'message' => 'Method not allowed']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $pollId = $input['poll_id'] ?? '';
        $ipAddress = $input['ip_address'] ?? '';

        if (empty($pollId) || empty($ipAddress)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Invalid input']);
        }

        $result = $this->votingEngine->releaseVote(intval($pollId), $ipAddress);

        // Log admin action
        if ($result['success']) {
            error_log("Admin {$_SESSION['user_email']} released vote for IP {$ipAddress} on poll {$pollId}");
        }

        http_response_code($result['success'] ? 200 : 400);
        return json_encode($result);
    }

    /**
     * Get vote history (AJAX)
     */
    public function getVoteHistory()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $pollId = $_GET['poll_id'] ?? '';
        $ipAddress = $_GET['ip_address'] ?? null;

        if (empty($pollId)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Poll ID required']);
        }

        $result = $this->votingEngine->getVoteHistory(intval($pollId), $ipAddress);
        http_response_code($result['success'] ? 200 : 400);

        return json_encode($result);
    }

    /**
     * Toggle poll status
     */
    public function togglePollStatus()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'message' => 'Method not allowed']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $pollId = $input['poll_id'] ?? '';
        $status = $input['status'] ?? '';

        if (empty($pollId) || empty($status) || !in_array($status, ['active', 'inactive'])) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Invalid input']);
        }

        $result = $this->pollModel->updateStatus(intval($pollId), $status);

        return json_encode([
            'success' => $result,
            'message' => $result ? 'Poll status updated' : 'Failed to update poll status'
        ]);
    }

    /**
     * Delete poll (admin only)
     */
    public function deletePoll()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'message' => 'Method not allowed']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $pollId = $input['poll_id'] ?? '';

        if (empty($pollId)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Poll ID required']);
        }

        $result = $this->pollModel->delete(intval($pollId));

        return json_encode([
            'success' => $result,
            'message' => $result ? 'Poll deleted' : 'Failed to delete poll'
        ]);
    }
}
