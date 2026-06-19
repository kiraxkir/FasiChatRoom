<?php

namespace App\Services;

class ConversationService
{
    private MessageService $messageService;

    public function __construct()
    {
        $this->messageService = new MessageService();
    }

    public function getInbox(array $user): array
    {
        $userId = (int)($user['id'] ?? 0);
        if ($userId <= 0) {
            return [];
        }

        return $this->messageService->findInbox($userId, $user);
    }

    public function getConversation(int $userId, int $targetId): array
    {
        if ($userId <= 0 || $targetId <= 0) {
            return [];
        }

        return $this->messageService->findConversation($userId, $targetId);
    }

    public function getSent(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        return $this->messageService->findSentBySender($userId);
    }
}
