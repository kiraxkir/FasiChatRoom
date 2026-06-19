<?php

namespace App\Controllers;

use App\Services\SessionManager;
use App\Services\MessageService;
use App\Services\ConversationService;
use App\Repositories\UtilisateurRepository;

class MessageController
{
    private MessageService $service;
    private ConversationService $conversation;
    private SessionManager $session;
    private UtilisateurRepository $userRepo;

    public function __construct()
    {
        $this->service = new MessageService();
        $this->conversation = new ConversationService();
        $this->session = SessionManager::getInstance();
        $this->userRepo = new UtilisateurRepository();
    }

    public function sendPrivate(): void
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
        $from = (int)$user['id'];
        $senderRole = $user['role'] ?? '';
        $to = (int)($_POST['to'] ?? 0);
        $contenu = trim($_POST['contenu'] ?? '');

        if (!$to || $contenu === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payload']);
            return;
        }

        $recipient = $this->userRepo->findById($to);
        if (!$recipient) {
            http_response_code(400);
            echo json_encode(['error' => 'Destinataire introuvable']);
            return;
        }

        $recipientRole = $recipient->getRole();

        if ($senderRole === 'etudiant' && $recipientRole !== 'etudiant') {
            http_response_code(403);
            echo json_encode(['error' => 'Les étudiants ne peuvent envoyer que des messages privés à d\'autres étudiants']);
            return;
        }

        $id = $this->service->sendPrivate($from, $to, $contenu);
        echo json_encode(['ok' => true, 'id' => $id]);
    }

    public function sendPublic(): void
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
        if ($role === 'etudiant') {
            http_response_code(403);
            echo json_encode(['error' => 'Les étudiants ne peuvent pas publier de messages publics']);
            return;
        }

        $from = (int)$user['id'];
        $contenu = trim($_POST['contenu'] ?? '');
        $promo = isset($_POST['promotion_id']) ? (int)$_POST['promotion_id'] : null;
        $cours = isset($_POST['cours_id']) ? (int)$_POST['cours_id'] : null;

        if ($contenu === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payload']);
            return;
        }

        $id = $this->service->sendPublic($from, $promo, $cours, $contenu);
        echo json_encode(['ok' => true, 'id' => $id]);
    }

    public function inbox(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
        $messages = $this->conversation->getInbox($user);
        echo json_encode(['ok' => true, 'messages' => $messages]);
    }

    public function conversation(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
        $targetId = isset($_GET['target_id']) ? (int)$_GET['target_id'] : 0;
        if (!$targetId) {
            http_response_code(400);
            echo json_encode(['error' => 'Parameter target_id required']);
            return;
        }

        $messages = $this->conversation->getConversation((int)$user['id'], $targetId);
        echo json_encode(['ok' => true, 'messages' => $messages]);
    }

    public function sent(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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
        $messages = $this->conversation->getSent((int)$user['id']);
        echo json_encode(['ok' => true, 'messages' => $messages]);
    }

    public function deleteMessage(): void
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
        $messageId = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;

        if (!$messageId) {
            http_response_code(400);
            echo json_encode(['error' => 'Parameter message_id required']);
            return;
        }

        $deleted = $this->service->delete($messageId, (int)$user['id']);
        if (!$deleted) {
            http_response_code(404);
            echo json_encode(['error' => 'Message not found or not owned by user']);
            return;
        }

        echo json_encode(['ok' => true]);
    }
}
