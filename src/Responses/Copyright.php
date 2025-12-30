<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string|null $email
 * @property-read string|null $phone
 * @property-read string|null $website
 * @property-read bool|null $active
 * @property-read bool|null $blocked
 * @property-read string|null $created_at
 * @property-read string|null $updated_at
 */
class Copyright extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->data['email'] ?? null;
    }

    public function isActive(): bool
    {
        return $this->data['active'] ?? true;
    }

    public function isBlocked(): bool
    {
        return $this->data['blocked'] ?? false;
    }
}
