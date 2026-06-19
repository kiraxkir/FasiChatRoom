<?php

namespace App\Models;

use App\Services\SessionManager;

abstract class Utilisateur
{
    protected int $id;
    protected string $nom;
    protected string $email;
    protected string $motDePasse;
    protected string $role;
    protected ?int $promotionId;
    protected string $dateCreation;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->nom = $data['nom'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->motDePasse = $data['motDePasse'] ?? '';
        $this->role = $data['role'] ?? '';
        $this->promotionId = $data['promotion_id'] ?? $data['promo_id'] ?? null;
        $this->dateCreation = $data['dateCreation'] ?? $data['created_at'] ?? date('Y-m-d H:i:s');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getPromotionId(): ?int
    {
        return $this->promotionId;
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->motDePasse);
    }

    public function login(): bool
    {
        return SessionManager::getInstance()->createSession($this);
    }

    public function logout(): void
    {
        SessionManager::getInstance()->destroySession();
    }

    public function isAuthenticated(): bool
    {
        return SessionManager::getInstance()->isAuthenticated();
    }
}
