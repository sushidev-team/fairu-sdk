<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Enums\SortingDirection;
use SushiDev\Fairu\Fragments\Predefined\AssetFragments;
use SushiDev\Fairu\Responses\AllFilesFlatResult;
use SushiDev\Fairu\Responses\Asset;
use SushiDev\Fairu\Responses\PaginatedList;

class AssetQueries extends BaseQuery
{
    protected string $resourceType = 'assets';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return AssetFragments::get($variant);
    }

    public function find(string $id, ?FragmentInterface $fragment = null): ?Asset
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuFile(\$id: ID!) {
            fairuFile(id: \$id) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('asset', ['id' => $id]);
        $result = $this->executeQuery($query, ['id' => $id], $cacheKey);

        if (! isset($result['fairuFile'])) {
            return null;
        }

        return new Asset($result['fairuFile']);
    }

    public function findByPath(string $path, ?FragmentInterface $fragment = null): ?Asset
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuFileByPath(\$path: String!) {
            fairuFileByPath(path: \$path) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('asset_path', ['path' => $path]);
        $result = $this->executeQuery($query, ['path' => $path], $cacheKey);

        if (! isset($result['fairuFileByPath'])) {
            return null;
        }

        return new Asset($result['fairuFileByPath']);
    }

    /**
     * Find an existing asset by its SHA1 content fingerprint (pre-upload
     * deduplication). Returns the oldest matching asset in the tenant, or null.
     */
    public function findByFingerprint(string $fingerprint, ?FragmentInterface $fragment = null): ?Asset
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuFileByFingerprint(\$fingerprint: String!) {
            fairuFileByFingerprint(fingerprint: \$fingerprint) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('asset_fingerprint', ['fingerprint' => $fingerprint]);
        $result = $this->executeQuery($query, ['fingerprint' => $fingerprint], $cacheKey);

        if (! isset($result['fairuFileByFingerprint'])) {
            return null;
        }

        return new Asset($result['fairuFileByFingerprint']);
    }

    public function findMany(array $ids, ?FragmentInterface $fragment = null): array
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuMultipleFiles(\$ids: [ID!]) {
            fairuMultipleFiles(ids: \$ids) {$selection}
        }
        GRAPHQL;

        $result = $this->executeQuery($query, ['ids' => $ids]);

        return array_map(
            fn ($item) => new Asset($item),
            $result['fairuMultipleFiles'] ?? []
        );
    }

    public function all(
        ?string $folderId = null,
        int $page = 1,
        int $perPage = 20,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuFiles(\$page: Int, \$perPage: Int, \$folder: ID) {
            fairuFiles(page: \$page, perPage: \$perPage, folder: \$folder) {
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

        $variables = [
            'page' => $page,
            'perPage' => $perPage,
            'folder' => $folderId,
        ];

        $result = $this->executeQuery($query, $variables);

        return new PaginatedList(
            $result['fairuFiles'] ?? [],
            fn ($item) => new Asset($item)
        );
    }

    public function search(
        string $search,
        int $page = 1,
        int $perPage = 20,
        ?string $orderBy = null,
        ?SortingDirection $orderDirection = null,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuSearch(\$search: String!, \$page: Int, \$perPage: Int, \$orderBy: String, \$orderDirection: FairuSortingDirection) {
            fairuSearch(search: \$search, page: \$page, perPage: \$perPage, orderBy: \$orderBy, orderDirection: \$orderDirection) {
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

        $variables = [
            'search' => $search,
            'page' => $page,
            'perPage' => $perPage,
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection?->value,
        ];

        $result = $this->executeQuery($query, array_filter($variables, fn ($v) => $v !== null));

        return new PaginatedList(
            $result['fairuSearch'] ?? [],
            fn ($item) => new Asset($item)
        );
    }

    public function totalSize(array $ids): int
    {
        $query = <<<'GRAPHQL'
        query FairuFilesTotalSize($ids: [ID!]!) {
            fairuFilesTotalSize(ids: $ids)
        }
        GRAPHQL;

        $result = $this->executeQuery($query, ['ids' => $ids]);

        return $result['fairuFilesTotalSize'] ?? 0;
    }

    public function allFlat(?string $afterCursor = null, ?int $limit = null): AllFilesFlatResult
    {
        $query = <<<'GRAPHQL'
        query FairuAllFilesFlat($afterCursor: String, $limit: Int) {
            fairuAllFilesFlat(afterCursor: $afterCursor, limit: $limit) {
                entries {
                    id
                    name
                    path
                    size
                    mime
                    isFolder
                    updatedAt
                }
                nextCursor
                hasMore
            }
        }
        GRAPHQL;

        $variables = array_filter([
            'afterCursor' => $afterCursor,
            'limit' => $limit,
        ], fn ($v) => $v !== null);

        $result = $this->executeQuery($query, $variables);

        return new AllFilesFlatResult($result['fairuAllFilesFlat'] ?? []);
    }
}
