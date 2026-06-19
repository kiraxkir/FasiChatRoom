<?php

namespace App\Services;

use App\Repositories\MessageRepository;

class MessageService
{
    private MessageRepository $repo;

    public function __construct()
    {
        $this->repo = new MessageRepository();
    }

    public function sendPrivate(int $from, int $to, string $contenu): int
    {
        return $this->repo->save([
            'expediteur_id' => $from,
            'destinataire_id' => $to,
            'contenu' => $contenu,
        ]);
    }

    public function sendPublic(int $from, ?int $promoId, ?int $coursId, string $contenu): int
    {
        return $this->repo->save([
            'expediteur_id' => $from,
            'promotion_id' => $promoId,
            'cours_id' => $coursId,
            'contenu' => $contenu,
        ]);
    }

    public function save(array $data): int
    {
        return $this->repo->save($data);
    }

    public function findById(int $id): ?array
    {
        return $this->repo->findById($id);
    }

    public function findInbox(int $userId, array $user): array
    {
        return $this->repo->findInbox($userId, $user);
    }

    public function findConversation(int $userId, int $targetId): array
    {
        return $this->repo->findConversation($userId, $targetId);
    }

    public function findSentBySender(int $userId): array
    {
        return $this->repo->findSentBySender($userId);
    }

    public function delete(int $messageId, int $userId): bool
    {
        return $this->repo->delete($messageId, $userId);
    }
}
