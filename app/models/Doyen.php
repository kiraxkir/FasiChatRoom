<?php

namespace App\Models;

use App\Models\Convocation;
use App\Interfaces\Convocable;

class Doyen extends Administrateur implements Convocable
{
    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    public function convoquer(Convocation $convocation): void
    {
        $convocation->envoyer();
    }
}
