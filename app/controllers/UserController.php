<?php

namespace App\Controllers;

use App\Services\SessionManager;
use App\Repositories\UtilisateurRepository;

class UserController
{
    private UtilisateurRepository $repo;
    private SessionManager $session;

    public function __construct()
    {
        $this->repo = new UtilisateurRepository();
        $this->session = SessionManager::getInstance();
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        if (!$this->session->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $user = $this->session->getUser();
        $role = $user['role'] ?? '';
        if (!in_array($role, ['doyen', 'vicedoyen'], true)) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $matricule = trim($_POST['matricule'] ?? '');
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? null);
        $motDePasse = $_POST['motDePasse'] ?? '';
        $roleTo = $_POST['role'] ?? 'etudiant';
        $promo = isset($_POST['promotion_id']) ? (int)$_POST['promotion_id'] : null;

        if ($matricule === '' || $nom === '' || $motDePasse === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $id = $this->repo->create([
            'matricule' => $matricule,
            'nom' => $nom,
            'email' => $email,
            'motDePasse' => $motDePasse,
            'role' => $roleTo,
            'promotion_id' => $promo,
        ]);

        echo json_encode(['ok' => true, 'id' => $id]);
    }

    public function list(): void
    {
        $users = $this->repo->findAll();
        echo json_encode($users);
    }
}
