<?php

namespace App\Models;

abstract class Fichier
{
    protected string $nom;
    protected int $taille;
    protected string $chemin;
    protected string $typeMime;

    public function __construct(array $data)
    {
        $this->nom = $data['nom'] ?? '';
        $this->taille = $data['taille'] ?? 0;
        $this->chemin = $data['chemin'] ?? '';
        $this->typeMime = $data['typeMime'] ?? '';
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getTaille(): int
    {
        return $this->taille;
    }

    public function getChemin(): string
    {
        return $this->chemin;
    }

    public function getTypeMime(): string
    {
        return $this->typeMime;
    }

    abstract public function validateMimeType(): bool;

    public function validateSize(): bool
    {
        return $this->taille <= 20 * 1024 * 1024;
    }
}
