<?php

namespace App\Services;

use App\Models\Utilisateur;
use App\Services\BaseDeDonnees;
use PDO;

class SessionManager
{
    private static ?SessionManager $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): SessionManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function createSession(Utilisateur $user): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => $user->getId(),
            'nom' => $user->getNom(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'promotion_id' => $user->getPromotionId(),
            'created_at' => $user->getDateCreation(),
        ];

        // persist session to database
        try {
            $pdo = BaseDeDonnees::getInstance()->getConnection();
            $sql = 'INSERT INTO sessions (id, user_id, session_data, created_at, updated_at) VALUES (:id, :user_id, :session_data, NOW(), NOW()) ON DUPLICATE KEY UPDATE session_data = :session_data_u, updated_at = NOW()';
            $stmt = $pdo->prepare($sql);
            $sessionData = json_encode($_SESSION['user']);
            $stmt->execute([
                ':id' => session_id(),
                ':user_id' => $user->getId(),
                ':session_data' => $sessionData,
                ':session_data_u' => $sessionData,
            ]);
        } catch (\Throwable $e) {
            // ignore DB session persistence errors but still return true for PHP session
        }

        return true;
    }

    public function destroySession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION = [];

        // remove from DB
        try {
            $pdo = BaseDeDonnees::getInstance()->getConnection();
            $stmt = $pdo->prepare('DELETE FROM sessions WHERE id = :id');
            $stmt->execute([':id' => session_id()]);
        } catch (\Throwable $e) {
            // ignore
        }

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public function isAuthenticated(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!empty($_SESSION['user'])) {
            return true;
        }

        // try to load from DB using session id
        try {
            $pdo = BaseDeDonnees::getInstance()->getConnection();
            $stmt = $pdo->prepare('SELECT session_data FROM sessions WHERE id = :id');
            $stmt->execute([':id' => session_id()]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['session_data'])) {
                $data = json_decode($row['session_data'], true);
                if (is_array($data)) {
                    $_SESSION['user'] = $data;
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        return false;
    }

    public function hasRole(string $role): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return isset($_SESSION['user']['role']) && in_array($_SESSION['user']['role'], $roles, true);
    }

    public function getUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return $_SESSION['user'];
    }
}
