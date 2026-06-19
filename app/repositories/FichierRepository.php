<?php

namespace App\Repositories;

use App\Services\BaseDeDonnees;
use PDO;

class FichierRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = BaseDeDonnees::getInstance()->getConnection();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO fichiers (nom, taille, chemin, typeMime, uploaded_by, uploaded_at) VALUES (:nom, :taille, :chemin, :type, :user, NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $data['nom'],
            ':taille' => $data['taille'],
            ':chemin' => $data['chemin'],
            ':type' => $data['typeMime'],
            ':user' => $data['uploaded_by'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM fichiers WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
