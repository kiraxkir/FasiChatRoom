<?php

namespace App\Models;

class Apparitaire extends Administrateur
{
    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
