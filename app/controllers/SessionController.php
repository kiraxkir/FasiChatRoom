<?php

namespace App\Controllers;

use App\Services\SessionManager;

class SessionController
{
    private SessionManager $session;

    public function __construct()
    {
        $this->session = SessionManager::getInstance();
    }

    public function profile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
            return;
        }

        if (!$this->session->isAuthenticated()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
            return;
        }

        $user = $this->session->getUser();
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'user' => $user]);
    }
}
