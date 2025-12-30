<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

class CopyrightDTO extends BaseDTO
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

    public function email(?string $email): self
    {
        $this->data['email'] = $email;

        return $this;
    }

    public function phone(?string $phone): self
    {
        $this->data['phone'] = $phone;

        return $this;
    }

    public function website(?string $website): self
    {
        $this->data['website'] = $website;

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
}
