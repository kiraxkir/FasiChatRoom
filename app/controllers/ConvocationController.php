<?php

namespace App\Controllers;

use App\Services\SessionManager;
use App\Services\ConvocationService;

class ConvocationController
{
    private ConvocationService $service;
    private SessionManager $session;

    public function __construct()
    {
        $this->service = new ConvocationService();
        $this->session = SessionManager::getInstance();
    }

    public function send(): void
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
        // only doyen or vicedoyen can send convocation
        if (!in_array($role, ['doyen', 'vicedoyen'], true)) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $data = [
            'expediteur_id' => $user['id'],
            'objet' => trim($_POST['objet'] ?? ''),
            'description' => $_POST['description'] ?? null,
            'dateReunion' => $_POST['dateReunion'] ?? null,
            'heure' => $_POST['heure'] ?? null,
            'lieu' => $_POST['lieu'] ?? null,
        ];

        if (empty($data['objet']) || empty($data['dateReunion']) || empty($data['heure'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            return;
        }

        $id = $this->service->createConvocation($data);
        echo json_encode(['ok' => true, 'id' => $id]);
    }

    public function list(): void
    {
        if (!$this->session->isAuthenticated()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $user = $this->session->getUser();
        $role = $user['role'] ?? '';
        if (!in_array($role, ['enseignant', 'assistant', 'doyen', 'vicedoyen'], true)) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $list = $this->service->listAll();
        echo json_encode($list);
    }
}
