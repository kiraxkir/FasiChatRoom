<?php

namespace App\Repositories;

use App\Services\BaseDeDonnees;
use PDO;

class ValveRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = BaseDeDonnees::getInstance()->getConnection();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO valve_announcements (author_id, title, content, category, target_role, created_at) VALUES (:author, :title, :content, :category, :target_role, NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':author' => $data['author_id'],
            ':title' => $data['title'],
            ':content' => $data['content'],
            ':category' => $data['category'] ?? 'information',
            ':target_role' => $data['target_role'] ?? 'all',
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findAll(string $role = 'all'): array
    {
        $stmt = $this->pdo->prepare('SELECT v.id, v.author_id, v.title, v.content, v.category, v.target_role, v.created_at, u.full_name AS author_name FROM valve_announcements v LEFT JOIN users u ON v.author_id = u.id WHERE v.target_role = :all OR v.target_role = :role ORDER BY v.created_at DESC');
        $stmt->execute([
            ':all' => 'all',
            ':role' => $role,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
