<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

use SushiDev\Fairu\Enums\DmcaStatus;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string|null $email
 * @property-read string|null $reply
 * @property-read bool|null $reply_send
 * @property-read string|null $status
 */
class Dmca extends BaseResponse
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

    public function getStatus(): ?DmcaStatus
    {
        $status = $this->data['status'] ?? null;

        return $status ? DmcaStatus::tryFrom($status) : null;
    }

    public function isReplySent(): bool
    {
        return $this->data['reply_send'] ?? false;
    }

    public function getFile(): ?Asset
    {
        if (isset($this->data['file'])) {
            return new Asset($this->data['file']);
        }

        return null;
    }
}
