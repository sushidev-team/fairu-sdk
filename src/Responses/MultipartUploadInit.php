<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

class MultipartUploadInit extends BaseResponse
{
    public function getId(): ?string
    {
        return $this->data['id'] ?? null;
    }

    public function getUploadId(): ?string
    {
        return $this->data['upload_id'] ?? null;
    }
}
