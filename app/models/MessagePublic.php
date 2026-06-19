<?php

namespace App\Models;

class MessagePublic extends Message
{
    private int $promotionId;
    private int $coursId;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->promotionId = $data['promotion_id'] ?? 0;
        $this->coursId = $data['cours_id'] ?? 0;
    }

    public function getPromotionId(): int
    {
        return $this->promotionId;
    }

    public function getCoursId(): int
    {
        return $this->coursId;
    }
}
