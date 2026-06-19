<?php

namespace App\Models;

class MessagePrive extends Message
{
    private int $destinataireId;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->destinataireId = $data['destinataire_id'] ?? 0;
    }

    public function getDestinataireId(): int
    {
        return $this->destinataireId;
    }
}
