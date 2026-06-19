<?php

namespace App\Models;

class AnnonceValve
{
    private int $id;
    private int $auteurId;
    private string $titre;
    private string $contenu;
    private string $datePublication;
    private ?string $dateExpiration;
    private string $categorie;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->auteurId = $data['auteur_id'] ?? 0;
        $this->titre = $data['titre'] ?? '';
        $this->contenu = $data['contenu'] ?? '';
        $this->datePublication = $data['datePublication'] ?? date('Y-m-d H:i:s');
        $this->dateExpiration = $data['dateExpiration'] ?? null;
        $this->categorie = $data['categorie'] ?? 'information';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuteurId(): int
    {
        return $this->auteurId;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getDatePublication(): string
    {
        return $this->datePublication;
    }

    public function getDateExpiration(): ?string
    {
        return $this->dateExpiration;
    }

    public function getCategorie(): string
    {
        return $this->categorie;
    }
}
