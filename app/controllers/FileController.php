<?php

namespace App\Controllers;

use App\Services\SessionManager;
use App\Services\FileService;

class FileController
{
    private FileService $service;
    private SessionManager $session;

    public function __construct(string $uploadPath)
    {
        $this->service = new FileService($uploadPath);
        $this->session = SessionManager::getInstance();
    }

    public function upload(): void
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

        if (empty($_FILES['file'])) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $user = $this->session->getUser();

        try {
            $res = $this->service->storeUpload($_FILES['file'], (int)$user['id']);
            echo json_encode(['ok' => true, 'file' => $res]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
