<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\UserFragments;
use SushiDev\Fairu\Responses\PaginatedList;
use SushiDev\Fairu\Responses\User;

class UserQueries extends BaseQuery
{
    protected string $resourceType = 'users';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return UserFragments::get($variant);
    }

    public function find(string $id, ?FragmentInterface $fragment = null): ?User
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuUser(\$id: ID!) {
            fairuUser(id: \$id) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('user', ['id' => $id]);
        $result = $this->executeQuery($query, ['id' => $id], $cacheKey);

        if (! isset($result['fairuUser'])) {
            return null;
        }

        return new User($result['fairuUser']);
    }

    public function all(
        int $page = 1,
        int $perPage = 20,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuUsers(\$page: Int, \$perPage: Int) {
            fairuUsers(page: \$page, perPage: \$perPage) {
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

        $cacheKey = $this->cacheManager?->generateKey('users', compact('page', 'perPage'));
        $result = $this->executeQuery($query, compact('page', 'perPage'), $cacheKey);

        return new PaginatedList(
            $result['fairuUsers'] ?? [],
            fn ($item) => new User($item)
        );
    }
}
