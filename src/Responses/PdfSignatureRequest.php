<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

use SushiDev\Fairu\Enums\PdfSignatureRequestStatus;

/**
 * @property-read string $id
 * @property-read string|null $status
 * @property-read array|null $emails
 * @property-read string|null $config_url
 * @property-read string|null $signature_id
 */
class PdfSignatureRequest extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getStatus(): ?PdfSignatureRequestStatus
    {
        $status = $this->data['status'] ?? null;

        return $status ? PdfSignatureRequestStatus::tryFrom($status) : null;
    }

    public function getEmails(): array
    {
        return $this->data['emails'] ?? [];
    }

    public function getConfigUrl(): ?string
    {
        return $this->data['config_url'] ?? null;
    }

    public function getSignatureId(): ?string
    {
        return $this->data['signature_id'] ?? null;
    }

    public function getFile(): ?Asset
    {
        if (isset($this->data['file'])) {
            return new Asset($this->data['file']);
        }

        return null;
    }

    public function isDone(): bool
    {
        return $this->getStatus() === PdfSignatureRequestStatus::DONE;
    }
}
