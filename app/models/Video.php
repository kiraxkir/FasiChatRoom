<?php

namespace App\Models;

class Video extends Fichier
{
    protected array $allowedMimeTypes = [
        'video/mp4',
        'video/webm',
        'video/ogg',
    ];

    public function validateMimeType(): bool
    {
        return in_array($this->typeMime, $this->allowedMimeTypes, true);
    }
}
