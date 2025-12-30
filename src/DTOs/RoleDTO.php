<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

class RoleDTO extends BaseDTO
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

    public function permissions(array $permissions): self
    {
        $this->data['permissions'] = $permissions;

        return $this;
    }
}
