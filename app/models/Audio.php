<?php

namespace App\Models;

class Audio extends Fichier
{
    protected array $allowedMimeTypes = [
        'audio/mpeg',
        'audio/wav',
        'audio/ogg',
    ];

    public function validateMimeType(): bool
    {
        return in_array($this->typeMime, $this->allowedMimeTypes, true);
    }
}
