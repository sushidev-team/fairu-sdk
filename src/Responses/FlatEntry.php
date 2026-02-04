<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read string $id
 * @property-read string $name
 * @property-read string $path
 * @property-read int $size
 * @property-read string|null $mime
 * @property-read bool $isFolder
 * @property-read string|null $updatedAt
 */
class FlatEntry extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    public function getPath(): string
    {
        return $this->data['path'];
    }

    public function getSize(): int
    {
        return $this->data['size'] ?? 0;
    }

    public function getMime(): ?string
    {
        return $this->data['mime'] ?? null;
    }

    public function isFolder(): bool
    {
        return $this->data['isFolder'] ?? false;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->data['updatedAt'] ?? null;
    }
}
