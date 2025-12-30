<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\RoleFragments;
use SushiDev\Fairu\Responses\PaginatedList;
use SushiDev\Fairu\Responses\Role;

class RoleQueries extends BaseQuery
{
    protected string $resourceType = 'roles';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return RoleFragments::get($variant);
    }

    public function find(string $id, ?FragmentInterface $fragment = null): ?Role
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuRole(\$id: ID!) {
            fairuRole(id: \$id) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('role', ['id' => $id]);
        $result = $this->executeQuery($query, ['id' => $id], $cacheKey);

        if (! isset($result['fairuRole'])) {
            return null;
        }

        return new Role($result['fairuRole']);
    }

    public function all(
        int $page = 1,
        int $perPage = 20,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuRoles(\$page: Int, \$perPage: Int) {
            fairuRoles(page: \$page, perPage: \$perPage) {
                data {$selection}
                paginatorInfo {
                    currentPage
                    lastPage
                    perPage
                    total
                    hasMorePages
                }
            }
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('roles', compact('page', 'perPage'));
        $result = $this->executeQuery($query, compact('page', 'perPage'), $cacheKey);

        return new PaginatedList(
            $result['fairuRoles'] ?? [],
            fn ($item) => new Role($item)
        );
    }
}
