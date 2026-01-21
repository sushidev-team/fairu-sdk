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
        // Note: The API does not return the parent folder in FairuFolderList
        $this->folder = null;

        // Parse the unified data array and separate by __typename
        $folders = [];
        $assets = [];

        foreach ($data['data'] ?? [] as $entry) {
            if (($entry['__typename'] ?? '') === 'FairuFolder') {
                $folders[] = new Folder($entry);
            } else {
                $assets[] = new Asset($entry);
            }
        }

        $this->folders = $folders;
        $this->assets = $assets;

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
