<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

class FolderDTO extends BaseDTO
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

    public function parent(?string $parent): self
    {
        $this->data['parent'] = $parent;

        return $this;
    }

    public function autoAssignCopyright(bool $autoAssign): self
    {
        $this->data['autoAssignCopyright'] = $autoAssign;

        return $this;
    }

    public function inheritCopyrightAssignment(bool $inherit): self
    {
        $this->data['inhertiCopyrightAssignment'] = $inherit;

        return $this;
    }

    public function copyrightIds(array $copyrightIds): self
    {
        $this->data['copyrightIds'] = $copyrightIds;

        return $this;
    }
}
