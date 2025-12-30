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

    public function find(string $id, ?FragmentInterface $fragment = null): ?Folder
    {
        return $this->content(
            folderId: $id,
            page: 1,
            perPage: 1,
            onlyFolder: true,
            fragment: $fragment
        )->folder;
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
        $assetSelection = $assetFragment
            ? $assetFragment->toGraphQL()
            : '{ id name mime url width height blurhash alt blocked has_error created_at }';

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
                folder {$folderSelection}
                folders {$folderSelection}
                assets {$assetSelection}
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
