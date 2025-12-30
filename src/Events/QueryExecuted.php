<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Events;

class QueryExecuted
{
    public function __construct(
        public readonly string $query,
        public readonly array $variables,
        public readonly array $response,
    ) {}
}
