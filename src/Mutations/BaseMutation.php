<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\Cache\CacheManager;
use SushiDev\Fairu\FairuClient;

abstract class BaseMutation
{
    public function __construct(
        protected readonly FairuClient $client,
        protected readonly ?CacheManager $cacheManager = null,
    ) {}

    protected function executeMutation(string $mutation, array $variables = []): array
    {
        return $this->client->mutate($mutation, $variables);
    }

    protected function invalidateCache(string ...$keys): void
    {
        if (! $this->cacheManager) {
            return;
        }

        foreach ($keys as $key) {
            $this->cacheManager->forget($key);
        }
    }
}
