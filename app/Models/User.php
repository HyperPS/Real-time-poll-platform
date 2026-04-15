<?php

namespace App\Models;

class User
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($name, $email, $passwordHash, $role = 'user')
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$name, $email, $passwordHash, $role]);
    }

    public function verifyPassword($email, $password)
    {
        $user = $this->findByEmail($email);
        if (!$user) return false;
        return password_verify($password, $user['password']);
    }

    public function isAdmin($userId)
    {
        $user = $this->findById($userId);
        return $user && $user['role'] === 'admin';
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllWithStats()
    {
        $stmt = $this->pdo->prepare("
            SELECT u.*, 
                   COUNT(DISTINCT v.id) as vote_count,
                   MAX(al.created_at) as last_activity
            FROM users u
            LEFT JOIN votes v ON u.id = v.user_id AND v.is_active = 1
            LEFT JOIN activity_logs al ON u.id = al.user_id
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $name, $email, $role)
    {
        $stmt = $this->pdo->prepare("
            UPDATE users SET name = ?, email = ?, role = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$name, $email, $role, $id]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateLastLogin($id)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function countByRole($role)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users WHERE role = ?");
        $stmt->execute([$role]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $row['count'];
    }
}
