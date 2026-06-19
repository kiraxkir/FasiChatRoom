<?php

namespace App\Services;

use App\Repositories\UtilisateurRepository;
use App\Models\Utilisateur;

class AuthService
{
    private UtilisateurRepository $repo;

    public function __construct()
    {
        $this->repo = new UtilisateurRepository();
    }

    public function loginByMatricule(string $matricule, string $password): ?Utilisateur
    {
        $user = $this->repo->authenticate($matricule, $password);
        return $user;
    }
}
