<?php

namespace App\Interfaces;

use App\Models\Convocation;

interface Convocable
{
    public function convoquer(Convocation $convocation): void;
}
