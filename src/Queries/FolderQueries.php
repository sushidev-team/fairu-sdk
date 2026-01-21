<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Enums\SortingDirection;
use SushiDev\Fairu\Fragments\Predefined\FolderFragments;
use SushiDev\Fairu\Responses\Folder;
use SushiDev\Fairu\Responses\FolderContent;

class FolderQueries extends BaseQuery
{
    protected string $resourceType = 'folders';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return FolderFragments::get($variant);
    }

    /**
     * Find a folder by ID.
     *
     * Note: The API does not support getting a folder by ID directly.
     * This method queries the folder's children and returns the first matching folder if found.
     * For more reliable results, use findByPath() if you know the folder's path.
     *
     * @deprecated Use findByPath() instead for more reliable results.
     */
    public function find(string $id, ?FragmentInterface $fragment = null): ?Folder
    {
        @trigger_error(
            'FolderQueries::find() is deprecated. Use findByPath() instead, as the API does not support fetching folders by ID directly.',
            E_USER_DEPRECATED
        );

        // The API doesn't support getting a folder by ID directly.
        // We can only list folder contents, not get the folder itself.
        return null;
    }

    public function findByPath(string $path, ?FragmentInterface $fragment = null): ?Folder
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuFolderByPath(\$path: String!) {
            fairuFolderByPath(path: \$path) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('folder_path', ['path' => $path]);
        $result = $this->executeQuery($query, ['path' => $path], $cacheKey);

        if (! isset($result['fairuFolderByPath'])) {
            return null;
        }

        return new Folder($result['fairuFolderByPath']);
    }

    public function content(
        ?string $folderId = null,
        int $page = 1,
        int $perPage = 20,
        ?string $search = null,
        bool $globalSearch = false,
        ?string $orderBy = null,
        ?SortingDirection $orderDirection = null,
        bool $onlyFolder = false,
        ?FragmentInterface $fragment = null,
        ?FragmentInterface $assetFragment = null
    ): FolderContent {
        $folderSelection = $this->getFragment($fragment);
        // Note: created_at is omitted from default asset selection to avoid type conflicts
        // with FairuFolder.created_at (DateTime vs String) until the API is updated.
        $assetSelection = $assetFragment
            ? $assetFragment->toGraphQL()
            : '{ id name mime url width height blurhash alt blocked has_error }';

        $query = <<<GRAPHQL
        query FairuFolder(
            \$page: Int,
            \$perPage: Int,
            \$folder: ID,
            \$search: String,
            \$globalSearch: Boolean,
            \$orderBy: String,
            \$orderDirection: FairuSortingDirection,
            \$onlyFolder: Boolean
        ) {
            fairuFolder(
                page: \$page,
                perPage: \$perPage,
                folder: \$folder,
                search: \$search,
                globalSearch: \$globalSearch,
                orderBy: \$orderBy,
                orderDirection: \$orderDirection,
                onlyFolder: \$onlyFolder
            ) {
                data {
                    __typename
                    ... on FairuFolder {$folderSelection}
                    ... on FairuAsset {$assetSelection}
                }
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
            'page' => $page,
            'perPage' => $perPage,
            'folder' => $folderId,
            'search' => $search,
            'globalSearch' => $globalSearch ?: null,
            'orderBy' => $orderBy,
            'orderDirection' => $orderDirection?->value,
            'onlyFolder' => $onlyFolder ?: null,
        ], fn ($v) => $v !== null);

        $cacheKey = $this->cacheManager?->generateKey('folder_content', $variables);
        $result = $this->executeQuery($query, $variables, $cacheKey);

        return new FolderContent($result['fairuFolder'] ?? []);
    }
}
