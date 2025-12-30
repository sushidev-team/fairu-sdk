<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

class FolderContent
{
    public readonly ?Folder $folder;

    public readonly array $folders;

    public readonly array $assets;

    public readonly ?PaginatorInfo $paginatorInfo;

    public function __construct(array $data)
    {
        $this->folder = isset($data['folder']) ? new Folder($data['folder']) : null;

        $this->folders = array_map(
            fn ($f) => new Folder($f),
            $data['folders'] ?? []
        );

        $this->assets = array_map(
            fn ($a) => new Asset($a),
            $data['assets'] ?? []
        );

        $this->paginatorInfo = isset($data['paginatorInfo'])
            ? new PaginatorInfo($data['paginatorInfo'])
            : null;
    }

    public function isEmpty(): bool
    {
        return empty($this->folders) && empty($this->assets);
    }

    public function count(): int
    {
        return count($this->folders) + count($this->assets);
    }
}
