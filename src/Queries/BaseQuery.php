<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Cache\CacheManager;
use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Fragments\FragmentRegistry;

abstract class BaseQuery
{
    protected bool $useCache = false;

    protected ?int $cacheTtl = null;

    protected string $resourceType = 'default';

    public function __construct(
        protected readonly FairuClient $client,
        protected readonly ?CacheManager $cacheManager = null,
        protected readonly ?FragmentRegistry $fragmentRegistry = null,
    ) {}

    public function cached(?int $ttl = null): static
    {
        $clone = clone $this;
        $clone->useCache = true;
        $clone->cacheTtl = $ttl;

        return $clone;
    }

    public function fresh(): static
    {
        $clone = clone $this;
        $clone->useCache = false;

        return $clone;
    }

    protected function executeQuery(string $query, array $variables = [], ?string $cacheKey = null): array
    {
        if ($this->useCache && $this->cacheManager && $cacheKey) {
            return $this->cacheManager->remember(
                $cacheKey,
                fn () => $this->client->query($query, $variables),
                $this->cacheTtl,
                $this->resourceType
            );
        }

        return $this->client->query($query, $variables);
    }

    protected function getFragment(?FragmentInterface $fragment, string $defaultVariant = 'default'): string
    {
        if ($fragment) {
            return $fragment->toGraphQL();
        }

        return $this->getDefaultFragment($defaultVariant)->toGraphQL();
    }

    abstract protected function getDefaultFragment(string $variant = 'default'): FragmentInterface;

    public function forget(string $key): bool
    {
        if ($this->cacheManager) {
            return $this->cacheManager->forget($key);
        }

        return false;
    }
}
