<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

class FileAccessSignature extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getSignature(): ?string
    {
        return $this->data['signature'] ?? null;
    }

    public function getExpiresAt(): ?string
    {
        return $this->data['expires_at'] ?? null;
    }
}
