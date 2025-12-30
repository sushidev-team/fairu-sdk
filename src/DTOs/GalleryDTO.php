<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

use DateTimeInterface;
use SushiDev\Fairu\Enums\GallerySortingField;
use SushiDev\Fairu\Enums\SortingDirection;

class GalleryDTO extends BaseDTO
{
    public function id(string $id): self
    {
        $this->data['id'] = $id;

        return $this;
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function description(?string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function folderId(?string $folderId): self
    {
        $this->data['folder_id'] = $folderId;

        return $this;
    }

    public function active(bool $active): self
    {
        $this->data['active'] = $active;

        return $this;
    }

    public function date(DateTimeInterface|string|null $date): self
    {
        if ($date instanceof DateTimeInterface) {
            $this->data['date'] = $date->format('Y-m-d H:i:s');
        } else {
            $this->data['date'] = $date;
        }

        return $this;
    }

    public function excludeFromList(bool $exclude): self
    {
        $this->data['exclude_from_list'] = $exclude;

        return $this;
    }

    public function location(?string $location): self
    {
        $this->data['location'] = $location;

        return $this;
    }

    public function sortingField(GallerySortingField|string|null $field): self
    {
        $this->data['sorting_field'] = $field instanceof GallerySortingField ? $field->value : $field;

        return $this;
    }

    public function sortingDirection(SortingDirection|string|null $direction): self
    {
        $this->data['sorting_direction'] = $direction instanceof SortingDirection ? $direction->value : $direction;

        return $this;
    }
}
