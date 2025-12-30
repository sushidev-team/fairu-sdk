<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string|null $description
 * @property-read string|null $date
 * @property-read string|null $location
 * @property-read bool|null $active
 * @property-read bool|null $exclude_from_list
 * @property-read string|null $sorting_field
 * @property-read string|null $sorting_direction
 * @property-read string|null $copyright_text
 */
class Gallery extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getDescription(): ?string
    {
        return $this->data['description'] ?? null;
    }

    public function isActive(): bool
    {
        return $this->data['active'] ?? true;
    }

    public function getCoverImage(): ?Asset
    {
        if (isset($this->data['cover_image'])) {
            return new Asset($this->data['cover_image']);
        }

        return null;
    }

    public function getItems(): array
    {
        $items = $this->data['items'] ?? [];

        return array_map(fn ($item) => new Asset($item), $items);
    }

    public function getCopyrights(): array
    {
        $copyrights = $this->data['copyrights'] ?? [];

        return array_map(fn ($c) => new Copyright($c), $copyrights);
    }
}
