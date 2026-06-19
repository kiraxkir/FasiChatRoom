<?php

namespace App\Middleware;

use App\Services\SessionManager;

class RoleMiddleware
{
    public static function requireRole(string $role): void
    {
        $session = SessionManager::getInstance();

        if (!$session->isAuthenticated() || !$session->hasRole($role)) {
            http_response_code(403);
            echo 'Accès refusé.';
            exit;
        }
    }
}
