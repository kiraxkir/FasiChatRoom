<?php

namespace App\Models;

use App\Interfaces\Convocable;
use App\Models\Convocation;

class ViceDoyen extends Administrateur implements Convocable
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
