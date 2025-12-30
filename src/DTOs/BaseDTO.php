<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

abstract class BaseDTO
{
    protected array $data = [];

    public static function make(): static
    {
        return new static();
    }

    public function toArray(): array
    {
        return array_filter($this->data, fn ($value) => $value !== null);
    }

    public function toGraphQLInput(): array
    {
        return $this->toArray();
    }
}
