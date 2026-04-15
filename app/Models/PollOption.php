<?php

namespace App\Models;

class PollOption
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getByPoll($pollId)
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM poll_options 
            WHERE poll_id = ? 
            ORDER BY id ASC
        ");
        $stmt->execute([$pollId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM poll_options WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($pollId, $optionText)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO poll_options (poll_id, option_text)
            VALUES (?, ?)
        ");
        $result = $stmt->execute([$pollId, $optionText]);
        return $result ? $this->pdo->lastInsertId() : false;
    }

    public function deleteByPoll($pollId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM poll_options WHERE poll_id = ?");
        return $stmt->execute([$pollId]);
    }
}
