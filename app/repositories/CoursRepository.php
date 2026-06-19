<?php

namespace App\Repositories;

use App\Services\BaseDeDonnees;
use PDO;

class CoursRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = BaseDeDonnees::getInstance()->getConnection();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM cours WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM cours ORDER BY titre ASC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByTeacher(int $teacherId): array
    {
        $stmt = $this->pdo->prepare('SELECT c.* FROM cours c JOIN enseignants_cours ec ON c.id = ec.cours_id WHERE ec.enseignant_id = :tid');
        $stmt->execute([':tid' => $teacherId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
