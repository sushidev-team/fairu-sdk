<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Cache;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Cache;

class CacheManager
{
    private ?CacheRepository $cache = null;

    public function __construct(
        private readonly mixed $cacheFactory,
        private readonly array $config = [],
    ) {}

    private function getCache(): CacheRepository
    {
        if ($this->cache === null) {
            $store = $this->config['store'] ?? null;
            $this->cache = $store ? Cache::store($store) : Cache::store();
        }

        return $this->cache;
    }

    public function isEnabled(): bool
    {
        return $this->config['enabled'] ?? true;
    }

    public function get(string $key, ?string $resourceType = null): mixed
    {
        if (! $this->isEnabled()) {
            return null;
        }

        return $this->getCache()->get($this->prefixKey($key));
    }

    public function put(string $key, mixed $value, ?int $ttl = null, ?string $resourceType = null): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $ttl = $ttl ?? $this->getTtl($resourceType);

        $this->getCache()->put($this->prefixKey($key), $value, $ttl);
    }

    public function remember(string $key, callable $callback, ?int $ttl = null, ?string $resourceType = null): mixed
    {
        if (! $this->isEnabled()) {
            return $callback();
        }

        $ttl = $ttl ?? $this->getTtl($resourceType);

        return $this->getCache()->remember($this->prefixKey($key), $ttl, $callback);
    }

    public function forget(string $key): bool
    {
        return $this->getCache()->forget($this->prefixKey($key));
    }

    public function forgetByPattern(string $pattern): void
    {
        // Note: Pattern-based deletion requires cache tags or a custom implementation
        // This is a simplified version that works with tagged caches
        $this->getCache()->flush();
    }

    public function forgetByResourceType(string $resourceType): void
    {
        // For tagged caches, this would use tags
        // For non-tagged caches, this is a no-op or requires custom implementation
    }

    public function flush(): bool
    {
        return $this->getCache()->flush();
    }

    private function prefixKey(string $key): string
    {
        $prefix = $this->config['prefix'] ?? 'fairu_';

        return $prefix.$key;
    }

    public function getTtl(?string $resourceType = null): int
    {
        if ($resourceType && isset($this->config['ttl'][$resourceType])) {
            return $this->config['ttl'][$resourceType];
        }

        return $this->config['ttl']['default'] ?? 600;
    }

    public function generateKey(string $operation, array $variables = []): string
    {
        return md5($operation.serialize($variables));
    }
}
