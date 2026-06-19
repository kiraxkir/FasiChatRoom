<?php

namespace App\Repositories;

use App\Services\BaseDeDonnees;
use PDO;

class ConvocationRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = BaseDeDonnees::getInstance()->getConnection();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO convocations (expediteur_id, objet, description, dateReunion, heure, lieu, dateEnvoi) VALUES (:exp, :objet, :desc, :dateReunion, :heure, :lieu, NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':exp' => $data['expediteur_id'],
            ':objet' => $data['objet'],
            ':desc' => $data['description'] ?? null,
            ':dateReunion' => $data['dateReunion'],
            ':heure' => $data['heure'],
            ':lieu' => $data['lieu'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function addRecipient(int $convocationId, int $userId): bool
    {
        $sql = 'INSERT INTO convocation_recipients (convocation_id, user_id) VALUES (:conv, :user)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':conv' => $convocationId, ':user' => $userId]);
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM convocations ORDER BY dateEnvoi DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
