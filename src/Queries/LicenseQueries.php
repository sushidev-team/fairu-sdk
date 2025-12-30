<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\LicenseFragments;
use SushiDev\Fairu\Responses\License;
use SushiDev\Fairu\Responses\PaginatedList;

class LicenseQueries extends BaseQuery
{
    protected string $resourceType = 'licenses';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return LicenseFragments::get($variant);
    }

    public function find(string $id, ?FragmentInterface $fragment = null): ?License
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuLicense(\$id: ID) {
            fairuLicense(id: \$id) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('license', ['id' => $id]);
        $result = $this->executeQuery($query, ['id' => $id], $cacheKey);

        if (! isset($result['fairuLicense'])) {
            return null;
        }

        return new License($result['fairuLicense']);
    }

    public function all(
        int $page = 1,
        int $perPage = 20,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuLicenses(\$page: Int, \$perPage: Int) {
            fairuLicenses(page: \$page, perPage: \$perPage) {
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

        $cacheKey = $this->cacheManager?->generateKey('licenses', compact('page', 'perPage'));
        $result = $this->executeQuery($query, compact('page', 'perPage'), $cacheKey);

        return new PaginatedList(
            $result['fairuLicenses'] ?? [],
            fn ($item) => new License($item)
        );
    }
}
