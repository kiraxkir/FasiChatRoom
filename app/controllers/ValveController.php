<?php

namespace App\Controllers;

use App\Services\SessionManager;
use App\Repositories\ValveRepository;

class ValveController
{
    private ValveRepository $repo;
    private SessionManager $session;

    public function __construct()
    {
        $this->repo = new ValveRepository();
        $this->session = SessionManager::getInstance();
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        if (!$this->session->isAuthenticated()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $user = $this->session->getUser();
        $role = $user['role'] ?? '';

        if ($role !== 'apparitaire') {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Forbidden']);
            return;
        }

        $data = [
            'author_id' => $user['id'],
            'title' => trim($_POST['titre'] ?? ''),
            'content' => trim($_POST['contenu'] ?? ''),
            'category' => $_POST['categorie'] ?? 'information',
            'target_role' => $_POST['target_role'] ?? 'all',
        ];

        if ($data['title'] === '' || $data['content'] === '') {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Titre et contenu requis']);
            return;
        }

        $id = $this->repo->create($data);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'id' => $id]);
    }

    public function list(): void
    {
        $user = $this->session->getUser();
        $role = $user['role'] ?? 'all';
        $rows = $this->repo->findAll($role);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($rows);
    }
}
