<?php

namespace App\Models;

class Convocation extends Message
{
    private string $objet;
    private string $dateReunion;
    private string $heure;
    private string $lieu;
    private string $description;

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->objet = $data['objet'] ?? '';
        $this->dateReunion = $data['dateReunion'] ?? '';
        $this->heure = $data['heure'] ?? '';
        $this->lieu = $data['lieu'] ?? '';
        $this->description = $data['description'] ?? '';
    }

    public function getObjet(): string
    {
        return $this->objet;
    }

    public function getDateReunion(): string
    {
        return $this->dateReunion;
    }

    public function getHeure(): string
    {
        return $this->heure;
    }

    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function envoyer(): bool
    {
        return true;
    }
}
