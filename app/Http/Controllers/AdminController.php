<?php

namespace App\Http\Controllers;

use App\Core\VotingEngine;
use App\Models\Poll;
use App\Models\User;

class AdminController
{
    private $votingEngine;
    private $pollModel;
    private $userModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->votingEngine = new VotingEngine($pdo);
        $this->pollModel = new Poll($pdo);
        $this->userModel = new User($pdo);
    }

    /**
     * Check admin access - returns true if authorized, sends response and returns false otherwise
     */
    private function requireAdmin()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
            return false;
        }
        return true;
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

        $polls = $this->pollModel->getAllWithVoteCounts();
        $users = $this->userModel->getAllWithStats();

        // Recent activity logs (last 50)
        $stmt = $this->pdo->prepare("
            SELECT al.*, u.name as user_name, u.email as user_email
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC
            LIMIT 50
        ");
        $stmt->execute();
        $activityLogs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Total vote count
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM votes WHERE is_active = 1");
        $stmt->execute();
        $totalVotes = (int) $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        // Counts for stat cards
        $totalPolls = $this->pollModel->countTotal();
        $activePolls = $this->pollModel->countActive();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $totalUsers = (int) $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

        return ['view' => 'admin/dashboard', 'data' => [
            'polls' => $polls,
            'users' => $users,
            'activityLogs' => $activityLogs,
            'totalVotes' => $totalVotes,
            'totalPolls' => $totalPolls,
            'activePolls' => $activePolls,
            'totalUsers' => $totalUsers,
        ]];
    }

    /**
     * Get activity logs with pagination (AJAX)
     */
    public function getActivityLogs()
    {
        header('Content-Type: application/json');

        if (!$this->requireAdmin()) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = min(100, max(1, intval($_GET['limit'] ?? 50)));
        $offset = ($page - 1) * $limit;
        $userIdFilter = isset($_GET['user_id']) && $_GET['user_id'] !== '' ? intval($_GET['user_id']) : null;
        $actionFilter = isset($_GET['action']) && $_GET['action'] !== '' ? $_GET['action'] : null;

        $where = [];
        $params = [];

        if ($userIdFilter !== null) {
            $where[] = 'al.user_id = ?';
            $params[] = $userIdFilter;
        }
        if ($actionFilter !== null) {
            $where[] = 'al.action = ?';
            $params[] = $actionFilter;
        }

        $whereClause = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM activity_logs al {$whereClause}";
        $stmt = $this->pdo->prepare($countSql);
        $stmt->execute($params);
        $total = (int) $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Get paginated results
        $sql = "
            SELECT al.*, u.name as user_name, u.email as user_email
            FROM activity_logs al
            LEFT JOIN users u ON al.user_id = u.id
            {$whereClause}
            ORDER BY al.created_at DESC
            LIMIT ? OFFSET ?
        ";
        $params[] = $limit;
        $params[] = $offset;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return json_encode([
            'success' => true,
            'data' => $logs,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * Create a new user (AJAX)
     */
    public function createUser()
    {
        header('Content-Type: application/json');

        if (!$this->requireAdmin()) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'message' => 'Method not allowed']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $role = $input['role'] ?? 'user';

        if (empty($name) || empty($email) || empty($password)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Name, email, and password are required']);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Invalid email address']);
        }

        if (!in_array($role, ['user', 'admin'])) {
            $role = 'user';
        }

        // Check if email already exists
        $existing = $this->userModel->findByEmail($email);
        if ($existing) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Email already exists']);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $result = $this->userModel->create($name, $email, $passwordHash, $role);

        if ($result) {
            log_activity($this->pdo, $_SESSION['user_id'], 'user_created', "Admin created user: {$email}");
        }

        return json_encode([
            'success' => $result ? true : false,
            'message' => $result ? 'User created successfully' : 'Failed to create user'
        ]);
    }

    /**
     * Delete a user (AJAX)
     */
    public function deleteUser()
    {
        header('Content-Type: application/json');

        if (!$this->requireAdmin()) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            return json_encode(['success' => false, 'message' => 'Method not allowed']);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $userId = intval($input['user_id'] ?? 0);

        if ($userId <= 0) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Valid user ID required']);
        }

        // Prevent self-deletion
        if ($userId === (int) $_SESSION['user_id']) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Cannot delete your own account']);
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'User not found']);
        }

        $result = $this->userModel->deleteUser($userId);

        if ($result) {
            log_activity($this->pdo, $_SESSION['user_id'], 'user_deleted', "Admin deleted user: {$user['email']}");
        }

        return json_encode([
            'success' => $result ? true : false,
            'message' => $result ? 'User deleted successfully' : 'Failed to delete user'
        ]);
    }

    /**
     * Get user stats (AJAX)
     */
    public function getUserStats()
    {
        header('Content-Type: application/json');

        if (!$this->requireAdmin()) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = intval($_GET['user_id'] ?? 0);
        if ($userId <= 0) {
            http_response_code(400);
            return json_encode(['success' => false, 'message' => 'Valid user ID required']);
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'User not found']);
        }
        unset($user['password']);

        // Get user's votes
        $stmt = $this->pdo->prepare("
            SELECT v.*, p.question as poll_question, po.option_text
            FROM votes v
            JOIN polls p ON v.poll_id = p.id
            JOIN poll_options po ON v.option_id = po.id
            WHERE v.user_id = ?
            ORDER BY v.voted_at DESC
        ");
        $stmt->execute([$userId]);
        $votes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get user's activity logs
        $stmt = $this->pdo->prepare("
            SELECT * FROM activity_logs
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 100
        ");
        $stmt->execute([$userId]);
        $activityLogs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return json_encode([
            'success' => true,
            'user' => $user,
            'votes' => $votes,
            'activity_logs' => $activityLogs
        ]);
    }

    /**
     * Get all votes across all polls for dashboard (AJAX)
     */
    public function getVotesForDashboard()
    {
        header('Content-Type: application/json');

        if (!$this->requireAdmin()) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $stmt = $this->pdo->prepare("
            SELECT v.*, 
                   u.name as user_name, u.email as user_email,
                   p.question as poll_question,
                   po.option_text
            FROM votes v
            LEFT JOIN users u ON v.user_id = u.id
            JOIN polls p ON v.poll_id = p.id
            JOIN poll_options po ON v.option_id = po.id
            ORDER BY v.voted_at DESC
        ");
        $stmt->execute();
        $votes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return json_encode([
            'success' => true,
            'data' => $votes
        ]);
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
