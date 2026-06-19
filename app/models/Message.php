<?php

namespace App\Models;

abstract class Message
{
    protected int $id;
    protected int $expediteurId;
    protected string $contenu;
    protected string $dateEnvoi;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->expediteurId = $data['expediteur_id'] ?? 0;
        $this->contenu = $data['contenu'] ?? '';
        $this->dateEnvoi = $data['dateEnvoi'] ?? date('Y-m-d H:i:s');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getExpediteurId(): int
    {
        return $this->expediteurId;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getDateEnvoi(): string
    {
        return $this->dateEnvoi;
    }
}
