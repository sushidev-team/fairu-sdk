<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Contracts;

interface FragmentInterface
{
    public function getName(): string;

    public function getTypeName(): string;

    public function toGraphQL(): string;

    public function getFields(): array;
}
