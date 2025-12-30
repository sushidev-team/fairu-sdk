<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string $access_key_id
 * @property-read string|null $bucket
 * @property-read array $permissions
 * @property-read bool $active
 * @property-read string|null $last_used_at
 * @property-read string|null $expires_at
 * @property-read string $created_at
 */
class RakuCredential extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getAccessKeyId(): string
    {
        return $this->data['access_key_id'];
    }

    public function getBucket(): ?string
    {
        return $this->data['bucket'] ?? null;
    }

    public function getPermissions(): array
    {
        return $this->data['permissions'] ?? [];
    }

    public function isActive(): bool
    {
        return $this->data['active'] ?? true;
    }

    public function getLastUsedAt(): ?string
    {
        return $this->data['last_used_at'] ?? null;
    }

    public function getExpiresAt(): ?string
    {
        return $this->data['expires_at'] ?? null;
    }
}
