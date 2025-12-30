<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Events;

class MutationExecuted
{
    public function __construct(
        public readonly string $mutation,
        public readonly array $variables,
        public readonly array $response,
    ) {}
}
