<?php

namespace App\Controllers;

use App\Repositories\UtilisateurRepository;
use App\Services\SessionManager;

class AuthController
{
    private UtilisateurRepository $repository;

    public function __construct()
    {
        $this->repository = new UtilisateurRepository();
    }

    public function showLogin(): void
    {
        require __DIR__ . '/../../public/login.html';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $matricule = trim(filter_input(INPUT_POST, 'matricule', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?: '');
        $motDePasse = $_POST['motDePasse'] ?? '';

        $user = $this->repository->authenticate($matricule, $motDePasse);

        header('Content-Type: application/json; charset=utf-8');

        if ($user !== null) {
            $user->login();
            echo json_encode([
                'success' => true,
                'role' => $user->getRole(),
            ]);
            exit;
        }

        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Identifiants invalides.']);
        exit;
    }

    public function logout(): void
    {
        $session = SessionManager::getInstance();
        $session->destroySession();
        header('Location: /login');
        exit;
    }
}
