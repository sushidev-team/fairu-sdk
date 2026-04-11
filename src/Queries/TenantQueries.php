<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\TenantFragments;
use SushiDev\Fairu\Responses\Tenant;

class TenantQueries extends BaseQuery
{
    protected string $resourceType = 'tenant';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return TenantFragments::get($variant);
    }

    public function get(?FragmentInterface $fragment = null): ?Tenant
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query {
            fairuTenant {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('tenant', []);
        $result = $this->executeQuery($query, [], $cacheKey);

        if (! isset($result['fairuTenant'])) {
            return null;
        }

        return new Tenant($result['fairuTenant']);
    }

    /**
     * Get all sub-tenants of the current tenant.
     *
     * @return array<int, Tenant>
     */
    public function subTenants(?FragmentInterface $fragment = null): array
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query {
            fairuSubTenants {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('sub_tenants', []);
        $result = $this->executeQuery($query, [], $cacheKey);

        return array_map(
            fn (array $data): Tenant => new Tenant($data),
            $result['fairuSubTenants'] ?? []
        );
    }
}
