<?php

namespace App\Models;

class Enseignant extends Utilisateur
{
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
