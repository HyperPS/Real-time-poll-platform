<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollOption;

class PollController
{
    private $pollModel;
    private $pollOptionModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->pollModel = new Poll($pdo);
        $this->pollOptionModel = new PollOption($pdo);
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $polls = $this->pollModel->getAllActive();
        return ['view' => 'polls/dashboard', 'data' => ['polls' => $polls]];
    }

    public function show($pollId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $poll = $this->pollModel->getWithOptions($pollId);
        if (!$poll) {
            $_SESSION['error'] = 'Poll not found';
            header('Location: /dashboard');
            exit;
        }

        return ['view' => 'polls/show', 'data' => ['poll' => $poll]];
    }

    /**
     * Get polls data (AJAX endpoint)
     */
    public function getPolls()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $polls = $this->pollModel->getAllActive();
        return json_encode([
            'success' => true,
            'polls' => $polls
        ]);
    }

    /**
     * Get single poll with options (AJAX endpoint)
     */
    public function getPoll($pollId)
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            return json_encode(['success' => false, 'message' => 'Unauthorized']);
        }

        $pollId = intval($pollId);
        $poll = $this->pollModel->getWithOptions($pollId);

        if (!$poll || $poll['status'] !== 'active') {
            http_response_code(404);
            return json_encode(['success' => false, 'message' => 'Poll not found']);
        }

        return json_encode([
            'success' => true,
            'poll' => $poll
        ]);
    }

    /**
     * Create new poll (Admin only)
     */
    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }

        $authController = new AuthController($this->pdo);
        if (!$authController->isAdmin()) {
            $_SESSION['error'] = 'Only admins can create polls';
            header('Location: /dashboard');
            exit;
        }

        return 'polls/create';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $authController = new AuthController($this->pdo);
        if (!$authController->isAdmin()) {
            http_response_code(403);
            exit;
        }

        $question = $_POST['question'] ?? '';
        $options = $_POST['options'] ?? [];

        if (empty($question) || empty($options)) {
            $_SESSION['error'] = 'Question and options are required';
            header('Location: /polls/create');
            exit;
        }

        // Create poll
        $pollId = $this->pollModel->create($question, $_SESSION['user_id']);

        // Create options
        foreach ($options as $option) {
            if (!empty(trim($option))) {
                $this->pollOptionModel->create($pollId, trim($option));
            }
        }

        $_SESSION['success'] = 'Poll created successfully';
        header('Location: /dashboard');
        exit;
    }
}
