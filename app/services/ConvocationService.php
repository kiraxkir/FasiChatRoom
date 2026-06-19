<?php

namespace App\Services;

use App\Repositories\ConvocationRepository;

class ConvocationService
{
    private ConvocationRepository $repo;

    public function __construct()
    {
        $this->repo = new ConvocationRepository();
    }

    public function createConvocation(array $data): int
    {
        return $this->repo->create($data);
    }

    public function addRecipient(int $convId, int $userId): bool
    {
        return $this->repo->addRecipient($convId, $userId);
    }

    public function listAll(): array
    {
        return $this->repo->findAll();
    }
}
