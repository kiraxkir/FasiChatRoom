<?php

namespace App\Repositories;

use App\Models\Etudiant;
use App\Models\Enseignant;
use App\Models\Assistant;
use App\Models\Doyen;
use App\Models\ViceDoyen;
use App\Models\Apparitaire;
use App\Models\Utilisateur;
use App\Services\BaseDeDonnees;
use PDO;

class UtilisateurRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = BaseDeDonnees::getInstance()->getConnection();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO users (matricule, full_name, email, password_hash, role_id, created_at, promo_id) VALUES (:matricule, :full_name, :email, :password_hash, :role_id, :created_at, :promo_id)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':matricule' => $data['matricule'] ?? '',
            ':full_name' => $data['nom'] ?? $data['full_name'] ?? '',
            ':email' => $data['email'] ?? null,
            ':password_hash' => password_hash($data['motDePasse'] ?? $data['password'] ?? '', PASSWORD_DEFAULT),
            ':role_id' => $data['role_id'] ?? $this->getRoleId($data['role'] ?? $data['role_name'] ?? ''),
            ':created_at' => $data['created_at'] ?? date('Y-m-d H:i:s'),
            ':promo_id' => $data['promo_id'] ?? $data['promotion_id'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function addUser(array $data): int
    {
        return $this->create($data);
    }

    public function findById(int $id): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT u.id, u.matricule, u.full_name AS nom, u.email, u.password_hash AS motDePasse, r.name AS role, u.created_at AS dateCreation, u.promo_id AS promotion_id FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = :id');
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch();

        return $data ? $this->hydrateUser($data) : null;
    }

    public function findByEmail(string $email): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT u.id, u.matricule, u.full_name AS nom, u.email, u.password_hash AS motDePasse, r.name AS role, u.created_at AS dateCreation, u.promo_id AS promotion_id FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.email = :email');
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();

        return $data ? $this->hydrateUser($data) : null;
    }

    public function findByMatricule(string $matricule): ?Utilisateur
    {
        $stmt = $this->pdo->prepare('SELECT u.id, u.matricule, u.full_name AS nom, u.email, u.password_hash AS motDePasse, r.name AS role, u.created_at AS dateCreation, u.promo_id AS promotion_id FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.matricule = :matricule');
        $stmt->execute([':matricule' => $matricule]);
        $data = $stmt->fetch();

        return $data ? $this->hydrateUser($data) : null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT u.id, u.matricule, u.full_name AS nom, u.email, u.password_hash AS motDePasse, r.name AS role, u.created_at AS dateCreation, u.promo_id AS promotion_id FROM users u LEFT JOIN roles r ON u.role_id = r.id');
        $users = [];

        while ($data = $stmt->fetch()) {
            $users[] = $this->hydrateUser($data);
        }

        return $users;
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['nom'])) {
            $fields[] = 'full_name = :full_name';
            $params[':full_name'] = $data['nom'];
        }
        if (isset($data['email'])) {
            $fields[] = 'email = :email';
            $params[':email'] = $data['email'];
        }
        if (isset($data['motDePasse'])) {
            $fields[] = 'password_hash = :password_hash';
            $params[':password_hash'] = password_hash($data['motDePasse'], PASSWORD_DEFAULT);
        }
        if (isset($data['role_id'])) {
            $fields[] = 'role_id = :role_id';
            $params[':role_id'] = $data['role_id'];
        }
        if (isset($data['role'])) {
            $fields[] = 'role_id = :role_id';
            $params[':role_id'] = $this->getRoleId($data['role']);
        }
        if (isset($data['promotion_id'])) {
            $fields[] = 'promo_id = :promo_id';
            $params[':promo_id'] = $data['promotion_id'];
        }
        if (isset($data['promo_id'])) {
            $fields[] = 'promo_id = :promo_id';
            $params[':promo_id'] = $data['promo_id'];
        }

        if (empty($fields)) {
            return false;
        }

        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function authenticate(string $email, string $password): ?Utilisateur
    {
        $matricule = $email;
        $user = $this->findByMatricule($matricule);

        if ($user && $user->verifyPassword($password)) {
            return $user;
        }

        return null;
    }

    private function hydrateUser(array $data): \App\Models\Utilisateur
    {
        return match ($data['role']) {
            'etudiant' => new Etudiant($data),
            'enseignant' => new Enseignant($data),
            'assistant' => new Assistant($data),
            'doyen' => new Doyen($data),
            'vicedoyen' => new ViceDoyen($data),
            'apparitaire' => new Apparitaire($data),
            default => new Etudiant($data),
        };
    }

    private function getRoleId(string $roleName): ?int
    {
        $stmt = $this->pdo->prepare('SELECT id FROM roles WHERE name = :name');
        $stmt->execute([':name' => $roleName]);
        $row = $stmt->fetch();

        return $row ? (int)$row['id'] : null;
    }
}
