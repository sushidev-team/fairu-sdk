<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

class PdfSignatureRequestDTO extends BaseDTO
{
    public function id(string $id): self
    {
        $this->data['id'] = $id;

        return $this;
    }

    public function emails(array $emails): self
    {
        $this->data['emails'] = $emails;

        return $this;
    }

    public function fileId(string $fileId): self
    {
        $this->data['file_id'] = $fileId;

        return $this;
    }
}
