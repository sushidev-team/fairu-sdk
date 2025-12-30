<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

use SushiDev\Fairu\Enums\UserStatus;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string|null $email
 * @property-read string|null $status
 * @property-read bool|null $owner
 */
class User extends BaseResponse
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

    public function getStatus(): ?UserStatus
    {
        $status = $this->data['status'] ?? null;

        return $status ? UserStatus::tryFrom($status) : null;
    }

    public function isOwner(): bool
    {
        return $this->data['owner'] ?? false;
    }

    public function isAccepted(): bool
    {
        return $this->getStatus() === UserStatus::ACCEPTED;
    }

    public function isPending(): bool
    {
        return $this->getStatus() === UserStatus::PENDING;
    }
}
