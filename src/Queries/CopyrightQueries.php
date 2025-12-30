<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\CopyrightFragments;
use SushiDev\Fairu\Responses\Copyright;
use SushiDev\Fairu\Responses\PaginatedList;

class CopyrightQueries extends BaseQuery
{
    protected string $resourceType = 'copyrights';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return CopyrightFragments::get($variant);
    }

    public function find(string $id, ?FragmentInterface $fragment = null): ?Copyright
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuCopyright(\$id: ID) {
            fairuCopyright(id: \$id) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('copyright', ['id' => $id]);
        $result = $this->executeQuery($query, ['id' => $id], $cacheKey);

        if (! isset($result['fairuCopyright'])) {
            return null;
        }

        return new Copyright($result['fairuCopyright']);
    }

    public function all(
        int $page = 1,
        int $perPage = 20,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuCopyrights(\$page: Int, \$perPage: Int) {
            fairuCopyrights(page: \$page, perPage: \$perPage) {
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

        $cacheKey = $this->cacheManager?->generateKey('copyrights', compact('page', 'perPage'));
        $result = $this->executeQuery($query, compact('page', 'perPage'), $cacheKey);

        return new PaginatedList(
            $result['fairuCopyrights'] ?? [],
            fn ($item) => new Copyright($item)
        );
    }
}
