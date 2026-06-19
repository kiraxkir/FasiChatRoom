<?php

namespace App\Models;

class Etudiant extends Utilisateur
{
    private ?int $promotionId;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->promotionId = $data['promotion_id'] ?? null;
    }

    public function getPromotionId(): ?int
    {
        return $this->promotionId;
    }
}
