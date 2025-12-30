<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

use SushiDev\Fairu\Enums\DiskType;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string|null $type
 * @property-read string|null $path
 * @property-read string|null $pattern
 * @property-read bool|null $active
 * @property-read bool|null $healthy
 * @property-read bool|null $syncing
 * @property-read bool|null $delete_at_origin
 * @property-read string|null $created_at
 * @property-read string|null $updated_at
 */
class Disk extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getType(): ?DiskType
    {
        $type = $this->data['type'] ?? null;

        return $type ? DiskType::tryFrom($type) : null;
    }

    public function isActive(): bool
    {
        return $this->data['active'] ?? true;
    }

    public function isHealthy(): bool
    {
        return $this->data['healthy'] ?? false;
    }

    public function isSyncing(): bool
    {
        return $this->data['syncing'] ?? false;
    }

    public function getFolder(): ?Folder
    {
        if (isset($this->data['folder'])) {
            return new Folder($this->data['folder']);
        }

        return null;
    }

    public function getCredentials(): ?DiskCredentials
    {
        if (isset($this->data['credentials'])) {
            return new DiskCredentials($this->data['credentials']);
        }

        return null;
    }
}
