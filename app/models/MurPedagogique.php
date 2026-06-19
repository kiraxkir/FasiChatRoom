<?php

namespace App\Models;

class MurPedagogique
{
    private int $id;
    private int $auteurId;
    private string $contenu;
    private string $type;
    private string $datePublication;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? 0;
        $this->auteurId = $data['auteur_id'] ?? 0;
        $this->contenu = $data['contenu'] ?? '';
        $this->type = $data['type'] ?? 'question';
        $this->datePublication = $data['datePublication'] ?? date('Y-m-d H:i:s');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuteurId(): int
    {
        return $this->auteurId;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDatePublication(): string
    {
        return $this->datePublication;
    }
}
