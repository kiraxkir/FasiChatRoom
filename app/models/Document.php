<?php

namespace App\Models;

class Document extends Fichier
{
    protected array $allowedMimeTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    public function validateMimeType(): bool
    {
        return in_array($this->typeMime, $this->allowedMimeTypes, true);
    }
}
