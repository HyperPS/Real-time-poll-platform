<?php

namespace App\Models;

class Poll
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllActive()
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM polls 
            WHERE status = 'active' 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM polls 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM polls WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($question, $userId)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO polls (question, status, created_by)
            VALUES (?, 'active', ?)
        ");
        $result = $stmt->execute([$question, $userId]);
        return $result ? $this->pdo->lastInsertId() : false;
    }

    public function updateStatus($pollId, $status)
    {
        $stmt = $this->pdo->prepare("
            UPDATE polls 
            SET status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$status, $pollId]);
    }

    public function delete($pollId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM polls WHERE id = ?");
        return $stmt->execute([$pollId]);
    }

    public function getWithOptions($pollId)
    {
        $poll = $this->findById($pollId);
        if (!$poll) return null;

        $stmt = $this->pdo->prepare("
            SELECT id, option_text 
            FROM poll_options 
            WHERE poll_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$pollId]);
        $poll['options'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $poll;
    }

    public function getAllWithVoteCounts()
    {
        $stmt = $this->pdo->prepare("
            SELECT p.*, COUNT(v.id) as vote_count
            FROM polls p
            LEFT JOIN votes v ON p.id = v.poll_id AND v.is_active = 1
            GROUP BY p.id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countTotal()
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM polls");
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $row['count'];
    }

    public function countActive()
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM polls WHERE status = 'active'");
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $row['count'];
    }
}
