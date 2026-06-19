<?php

namespace App\Controllers;

use App\Services\SessionManager;
use App\Repositories\CoursRepository;
use App\Services\BaseDeDonnees;

class CourseController
{
    private CoursRepository $repo;
    private SessionManager $session;
    private $pdo;

    public function __construct()
    {
        $this->repo = new CoursRepository();
        $this->session = SessionManager::getInstance();
        $this->pdo = BaseDeDonnees::getInstance()->getConnection();
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

        // Only Doyen or Vice-Doyen can create and assign courses
        if (!in_array($role, ['doyen', 'vicedoyen'], true)) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }

        $code = trim($_POST['code'] ?? '');
        $titre = trim($_POST['titre'] ?? '');
        $description = $_POST['description'] ?? null;
        $teacherId = isset($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : null;
        $promotionId = isset($_POST['promotion_id']) ? (int)$_POST['promotion_id'] : null;

        if ($code === '' || $titre === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Missing fields']);
            return;
        }

        // validate teacher exists if provided
        if ($teacherId !== null) {
            $stmt = $this->pdo->prepare('SELECT id FROM utilisateurs WHERE id = :id');
            $stmt->execute([':id' => $teacherId]);
            if (!$stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['error' => 'Teacher not found']);
                return;
            }
        }

        $sql = 'INSERT INTO cours (code, titre, description, teacher_id, promotion_id, is_active, created_at) VALUES (:code, :titre, :desc, :teacher, :promo, 1, NOW())';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':code' => $code, ':titre' => $titre, ':desc' => $description, ':teacher' => $teacherId, ':promo' => $promotionId]);
        $id = (int)$this->pdo->lastInsertId();

        echo json_encode(['ok' => true, 'id' => $id]);
    }

    public function list(): void
    {
        $rows = $this->repo->findAll();
        echo json_encode($rows);
    }
}
