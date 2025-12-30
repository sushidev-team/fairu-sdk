<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

class UploadLink extends BaseResponse
{
    public function getId(): ?string
    {
        return $this->data['id'] ?? null;
    }

    public function getUrl(): ?string
    {
        return $this->data['url'] ?? null;
    }
}
