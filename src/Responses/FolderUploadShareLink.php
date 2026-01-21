<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

class FolderUploadShareLink extends BaseResponse
{
    public function getId(): ?string
    {
        return $this->data['id'] ?? null;
    }

    public function getUrl(): ?string
    {
        return $this->data['url'] ?? null;
    }

    public function getExpiresAt(): ?string
    {
        return $this->data['expires_at'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getFolderId(): ?string
    {
        return $this->data['folder_id'] ?? null;
    }
}
