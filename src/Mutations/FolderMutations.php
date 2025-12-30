<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\FolderDTO;
use SushiDev\Fairu\Enums\UploadShareLinkExpiration;
use SushiDev\Fairu\Responses\Disk;
use SushiDev\Fairu\Responses\Folder;
use SushiDev\Fairu\Responses\FolderUploadShareLink;

class FolderMutations extends BaseMutation
{
    public function create(FolderDTO $data): ?Folder
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuFolder($data: FairuFolderDTO!) {
            createFairuFolder(data: $data) {
                id
                name
                auto_assign_copyright
                created_at
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['createFairuFolder'])) {
            return null;
        }

        return new Folder($result['createFairuFolder']);
    }

    public function update(FolderDTO $data): ?Folder
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuFolder($data: FairuFolderDTO!) {
            updateFairuFolder(data: $data) {
                id
                name
                auto_assign_copyright
                updated_at
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['updateFairuFolder'])) {
            return null;
        }

        return new Folder($result['updateFairuFolder']);
    }

    public function delete(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuFolder($id: ID!) {
            deleteFairuFolder(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['deleteFairuFolder'] ?? false;
    }

    public function rename(string $id, string $name): ?Folder
    {
        $mutation = <<<'GRAPHQL'
        mutation RenameFairuFolder($id: ID!, $name: String!) {
            renameFairuFolder(id: $id, name: $name) {
                id
                name
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id, 'name' => $name]);

        if (! isset($result['renameFairuFolder'])) {
            return null;
        }

        return new Folder($result['renameFairuFolder']);
    }

    public function move(string $id, ?string $parentId = null): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation MoveFairuFolder($id: ID!, $parent: ID) {
            moveFairuFolder(id: $id, parent: $parent)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id, 'parent' => $parentId]);

        return $result['moveFairuFolder'] ?? false;
    }

    public function createFtp(string $id): ?Disk
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuFolderFTP($id: ID!) {
            createFairuFolderFTP(id: $id) {
                id
                name
                type
                active
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        if (! isset($result['createFairuFolderFTP'])) {
            return null;
        }

        return new Disk($result['createFairuFolderFTP']);
    }

    public function createUploadShareLink(
        string $id,
        ?UploadShareLinkExpiration $expiresIn = null,
        ?string $name = null
    ): FolderUploadShareLink {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuFolderUploadShareLink($id: ID!, $expires_in: FairuUploadShareLinkExpiration, $name: String) {
            createFairuFolderUploadShareLink(id: $id, expires_in: $expires_in, name: $name) {
                url
                expires_at
            }
        }
        GRAPHQL;

        $variables = array_filter([
            'id' => $id,
            'expires_in' => $expiresIn?->value,
            'name' => $name,
        ], fn ($v) => $v !== null);

        $result = $this->executeMutation($mutation, $variables);

        return new FolderUploadShareLink($result['createFairuFolderUploadShareLink'] ?? []);
    }
}
