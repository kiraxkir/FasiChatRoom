<?php

namespace App\Models;

class Assistant extends Utilisateur
{
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
