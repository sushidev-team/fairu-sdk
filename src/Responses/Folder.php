<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read bool|null $auto_assign_copyright
 * @property-read string|null $created_at
 * @property-read string|null $updated_at
 */
class Folder extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function hasAutoAssignCopyright(): bool
    {
        return $this->data['auto_assign_copyright'] ?? false;
    }

    public function getCopyrights(): array
    {
        $copyrights = $this->data['copyrights'] ?? [];

        return array_map(fn ($c) => new Copyright($c), $copyrights);
    }
}
