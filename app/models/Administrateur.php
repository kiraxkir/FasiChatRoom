<?php

namespace App\Models;

abstract class Administrateur extends Utilisateur
{
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
