<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\DmcaFragments;
use SushiDev\Fairu\Responses\Dmca;
use SushiDev\Fairu\Responses\PaginatedList;

class DmcaQueries extends BaseQuery
{
    protected string $resourceType = 'dmcas';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return DmcaFragments::get($variant);
    }

    public function find(string $id, ?FragmentInterface $fragment = null): ?Dmca
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuDmca(\$id: ID!) {
            fairuDmca(id: \$id) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('dmca', ['id' => $id]);
        $result = $this->executeQuery($query, ['id' => $id], $cacheKey);

        if (! isset($result['fairuDmca'])) {
            return null;
        }

        return new Dmca($result['fairuDmca']);
    }

    public function all(
        int $page = 1,
        int $perPage = 20,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuDmcas(\$page: Int, \$perPage: Int) {
            fairuDmcas(page: \$page, perPage: \$perPage) {
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

        $cacheKey = $this->cacheManager?->generateKey('dmcas', compact('page', 'perPage'));
        $result = $this->executeQuery($query, compact('page', 'perPage'), $cacheKey);

        return new PaginatedList(
            $result['fairuDmcas'] ?? [],
            fn ($item) => new Dmca($item)
        );
    }
}
