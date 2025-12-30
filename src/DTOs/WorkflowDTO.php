<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

use SushiDev\Fairu\Enums\WorkflowType;

class WorkflowDTO extends BaseDTO
{
    public function id(string $id): self
    {
        $this->data['id'] = $id;

        return $this;
    }

    public function type(WorkflowType|string $type): self
    {
        $this->data['type'] = $type instanceof WorkflowType ? $type->value : $type;

        return $this;
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function active(bool $active): self
    {
        $this->data['active'] = $active;

        return $this;
    }

    public function structure(array $structure): self
    {
        $this->data['structure'] = $structure;

        return $this;
    }
}
