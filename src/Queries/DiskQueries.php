<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\DiskFragments;
use SushiDev\Fairu\Responses\Disk;
use SushiDev\Fairu\Responses\DiskStatus;
use SushiDev\Fairu\Responses\PaginatedList;

class DiskQueries extends BaseQuery
{
    protected string $resourceType = 'disks';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return DiskFragments::get($variant);
    }

    public function find(string $id, ?FragmentInterface $fragment = null): ?Disk
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuDisk(\$id: ID!) {
            fairuDisk(id: \$id) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('disk', ['id' => $id]);
        $result = $this->executeQuery($query, ['id' => $id], $cacheKey);

        if (! isset($result['fairuDisk'])) {
            return null;
        }

        return new Disk($result['fairuDisk']);
    }

    public function all(
        int $page = 1,
        int $perPage = 20,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuDisks(\$page: Int, \$perPage: Int) {
            fairuDisks(page: \$page, perPage: \$perPage) {
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

        $cacheKey = $this->cacheManager?->generateKey('disks', compact('page', 'perPage'));
        $result = $this->executeQuery($query, compact('page', 'perPage'), $cacheKey);

        return new PaginatedList(
            $result['fairuDisks'] ?? [],
            fn ($item) => new Disk($item)
        );
    }

    public function status(string $id): DiskStatus
    {
        $query = <<<'GRAPHQL'
        query FairuDiskStatus($id: ID!) {
            fairuDiskStatus(id: $id) {
                id
                syncing
                open
                pending
                synced
                failed
            }
        }
        GRAPHQL;

        $result = $this->executeQuery($query, ['id' => $id]);

        return new DiskStatus($result['fairuDiskStatus'] ?? []);
    }
}
