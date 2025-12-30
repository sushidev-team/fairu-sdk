<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

class FileDTO extends BaseDTO
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

    public function alt(?string $alt): self
    {
        $this->data['alt'] = $alt;

        return $this;
    }

    public function description(?string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function caption(?string $caption): self
    {
        $this->data['caption'] = $caption;

        return $this;
    }

    public function active(bool $active): self
    {
        $this->data['active'] = $active;

        return $this;
    }

    public function blocked(bool $blocked): self
    {
        $this->data['blocked'] = $blocked;

        return $this;
    }

    public function focalPoint(?string $focalPoint): self
    {
        $this->data['focal_point'] = $focalPoint;

        return $this;
    }

    public function copyrightIds(array $copyrightIds): self
    {
        $this->data['copyrightIds'] = $copyrightIds;

        return $this;
    }

    public function licenseIds(array $licenseIds): self
    {
        $this->data['licenseIds'] = $licenseIds;

        return $this;
    }
}
