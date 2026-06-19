<?php

namespace App\Repositories;

use App\Services\BaseDeDonnees;
use PDO;

class MessageRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = BaseDeDonnees::getInstance()->getConnection();
    }

    public function save(array $data): int
    {
        if (!empty($data['destinataire_id'])) {
            $sql = 'INSERT INTO messages (expediteur_id, contenu, dateEnvoi, message_type, destinataire_id) VALUES (:exp, :contenu, NOW(), "prive", :dest)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':exp' => $data['expediteur_id'], ':contenu' => $data['contenu'], ':dest' => $data['destinataire_id']]);
            return (int)$this->pdo->lastInsertId();
        }

        $sql = 'INSERT INTO messages (expediteur_id, contenu, dateEnvoi, message_type, promotion_id, cours_id) VALUES (:exp, :contenu, NOW(), "public", :promo, :cours)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':exp' => $data['expediteur_id'],
            ':contenu' => $data['contenu'],
            ':promo' => $data['promotion_id'] ?? null,
            ':cours' => $data['cours_id'] ?? null,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM messages WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findInbox(int $userId, array $user): array
    {
        $stmt = $this->pdo->prepare('SELECT m.*, exp.full_name AS expediteur_nom, exp.role AS expediteur_role, dest.full_name AS destinataire_nom, dest.role AS destinataire_role FROM messages m JOIN users exp ON m.expediteur_id = exp.id LEFT JOIN users dest ON m.destinataire_id = dest.id WHERE m.message_type = "prive" AND m.destinataire_id = :userId ORDER BY m.dateEnvoi DESC');
        $stmt->execute([':userId' => $userId]);
        $private = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (in_array($user['role'], ['etudiant', 'enseignant', 'assistant'], true)) {
            $stmt = $this->pdo->prepare('SELECT m.*, u.full_name AS expediteur_nom, u.role AS expediteur_role FROM messages m JOIN users u ON m.expediteur_id = u.id WHERE m.message_type = "public" AND (m.promotion_id = :promo OR m.cours_id IN (SELECT cours_id FROM etudiants_cours WHERE etudiant_id = :userId) OR m.cours_id IN (SELECT cours_id FROM enseignants_cours WHERE enseignant_id = :userId)) ORDER BY m.dateEnvoi DESC');
            $stmt->execute([':promo' => $user['promotion_id'] ?? null, ':userId' => $userId]);
            $public = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $public = [];
        }

        return array_merge($private, $public);
    }

    public function findConversation(int $userId, int $targetId): array
    {
        $stmt = $this->pdo->prepare('SELECT m.*, u.full_name AS expediteur_nom, u.role AS expediteur_role FROM messages m JOIN users u ON m.expediteur_id = u.id WHERE m.message_type = "prive" AND ((m.expediteur_id = :userId AND m.destinataire_id = :targetId) OR (m.expediteur_id = :targetId AND m.destinataire_id = :userId)) ORDER BY m.dateEnvoi ASC');
        $stmt->execute([':userId' => $userId, ':targetId' => $targetId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findSentBySender(int $userId): array
    {
        $stmt = $this->pdo->prepare('SELECT m.*, dest.full_name AS destinataire_nom, dest.role AS destinataire_role, exp.full_name AS expediteur_nom, exp.role AS expediteur_role FROM messages m LEFT JOIN users dest ON m.destinataire_id = dest.id LEFT JOIN users exp ON m.expediteur_id = exp.id WHERE m.expediteur_id = :userId ORDER BY m.dateEnvoi DESC');
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM messages WHERE id = :id AND expediteur_id = :userId');
        return $stmt->execute([':id' => $id, ':userId' => $userId]);
    }
}
