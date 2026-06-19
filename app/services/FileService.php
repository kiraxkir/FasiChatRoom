<?php

namespace App\Services;

use App\Repositories\FichierRepository;

class FileService
{
    private FichierRepository $repo;
    private string $uploadPath;

    public function __construct(string $uploadPath)
    {
        $this->repo = new FichierRepository();
        $this->uploadPath = rtrim($uploadPath, DIRECTORY_SEPARATOR);
    }

    public function storeUpload(array $file, int $uploadedBy): array
    {
        $originalName = $file['name'];
        $tmp = $file['tmp_name'];
        $size = (int)$file['size'];
        $type = $file['type'];

        $safeName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
        $dest = $this->uploadPath . DIRECTORY_SEPARATOR . $safeName;

        if (!move_uploaded_file($tmp, $dest)) {
            throw new \RuntimeException('Upload failed');
        }

        $id = $this->repo->create([
            'nom' => $originalName,
            'taille' => $size,
            'chemin' => $dest,
            'typeMime' => $type,
            'uploaded_by' => $uploadedBy,
        ]);

        return ['id' => $id, 'path' => $dest];
    }
}
