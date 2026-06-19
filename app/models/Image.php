<?php

namespace App\Models;

class Image extends Fichier
{
    protected array $allowedMimeTypes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
    ];

    public function validateMimeType(): bool
    {
        return in_array($this->typeMime, $this->allowedMimeTypes, true);
    }
}
