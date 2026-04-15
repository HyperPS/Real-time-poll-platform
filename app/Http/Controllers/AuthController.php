<?php

namespace App\Http\Controllers;

use App\Models\User;

class AuthController
{
    private $userModel;
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    public function showLoginForm()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        return 'auth/login';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return 'auth/login';
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Validate input
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Email and password are required';
            return 'auth/login';
        }

        // Verify credentials
        if ($this->userModel->verifyPassword($email, $password)) {
            $user = $this->userModel->findByEmail($email);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];

            // Update last login timestamp
            $this->userModel->updateLastLogin($user['id']);

            // Log successful login
            log_activity($this->pdo, $user['id'], 'login_success', 'User logged in: ' . $user['email']);

            header('Location: /dashboard');
            exit;
        } else {
            // Log failed login attempt
            log_activity($this->pdo, null, 'login_failed', 'Failed login attempt for: ' . $email);

            $_SESSION['error'] = 'Invalid email or password';
            return 'auth/login';
        }
    }

    public function logout()
    {
        session_destroy();
        header('Location: /');
        exit;
    }

    public function isAuthenticated()
    {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
}
