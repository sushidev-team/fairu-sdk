<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\GalleryFragments;
use SushiDev\Fairu\Responses\Gallery;
use SushiDev\Fairu\Responses\PaginatedList;

class GalleryQueries extends BaseQuery
{
    protected string $resourceType = 'galleries';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return GalleryFragments::get($variant);
    }

    public function find(string $id, ?FragmentInterface $fragment = null): ?Gallery
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuGallery(\$id: ID!) {
            fairuGallery(id: \$id) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('gallery', ['id' => $id]);
        $result = $this->executeQuery($query, ['id' => $id], $cacheKey);

        if (! isset($result['fairuGallery'])) {
            return null;
        }

        return new Gallery($result['fairuGallery']);
    }

    public function all(
        array $tenants,
        int $page = 1,
        int $perPage = 20,
        ?string $from = null,
        ?string $until = null,
        ?string $search = null,
        ?string $orderBy = null,
        ?string $orderDirection = null,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuGalleries(
            \$tenants: [ID!]!,
            \$page: Int,
            \$perPage: Int,
            \$from: String,
            \$until: String,
            \$search: String,
            \$orderBy: String,
            \$orderDirection: String
        ) {
            fairuGalleries(
                tenants: \$tenants,
                page: \$page,
                perPage: \$perPage,
                from: \$from,
                until: \$until,
                search: \$search,
                orderBy: \$orderBy,
                orderDirection: \$orderDirection
            ) {
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

        $variables = array_filter([
            'tenants' => $tenants,
            'page' => $page,
            'perPage' => $perPage,
            'from' => $from,
            'until' => $until,
            'search' => $search,
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection,
        ], fn ($v) => $v !== null);

        $result = $this->executeQuery($query, $variables);

        return new PaginatedList(
            $result['fairuGalleries'] ?? [],
            fn ($item) => new Gallery($item)
        );
    }
}
