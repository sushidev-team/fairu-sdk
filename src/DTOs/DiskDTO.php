<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

use SushiDev\Fairu\Enums\DiskType;

class DiskDTO extends BaseDTO
{
    public function id(string $id): self
    {
        $this->data['id'] = $id;

        return $this;
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function folderId(?string $folderId): self
    {
        $this->data['folder_id'] = $folderId;

        return $this;
    }

    public function type(DiskType|string $type): self
    {
        $this->data['type'] = $type instanceof DiskType ? $type->value : $type;

        return $this;
    }

    public function path(?string $path): self
    {
        $this->data['path'] = $path;

        return $this;
    }

    public function pattern(?string $pattern): self
    {
        $this->data['pattern'] = $pattern;

        return $this;
    }

    public function active(bool $active): self
    {
        $this->data['active'] = $active;

        return $this;
    }

    public function deleteAtOrigin(bool $delete): self
    {
        $this->data['delete_at_origin'] = $delete;

        return $this;
    }

    public function credentials(DiskCredentialsDTO|array $credentials): self
    {
        $this->data['credentials'] = $credentials instanceof DiskCredentialsDTO
            ? $credentials->toArray()
            : $credentials;

        return $this;
    }
}
