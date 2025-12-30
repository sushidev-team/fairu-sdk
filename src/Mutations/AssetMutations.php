<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\FileDTO;
use SushiDev\Fairu\Enums\VideoVersions;
use SushiDev\Fairu\Responses\Asset;
use SushiDev\Fairu\Responses\FileAccessSignature;
use SushiDev\Fairu\Responses\UploadLink;

class AssetMutations extends BaseMutation
{
    public function update(FileDTO $data): ?Asset
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuFile($data: FairuFileDTO!) {
            updateFairuFile(data: $data) {
                id
                name
                mime
                alt
                caption
                description
                url
                width
                height
                blocked
                has_error
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['updateFairuFile'])) {
            return null;
        }

        return new Asset($result['updateFairuFile']);
    }

    public function delete(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuFile($id: ID!) {
            deleteFairuFile(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['deleteFairuFile'] ?? false;
    }

    public function block(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation BlockFairuFile($id: ID!) {
            blockFairuFile(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['blockFairuFile'] ?? false;
    }

    public function unblock(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation UnblockFairuFile($id: ID!) {
            unblockFairuFile(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['unblockFairuFile'] ?? false;
    }

    public function rename(string $id, string $name): ?Asset
    {
        $mutation = <<<'GRAPHQL'
        mutation RenameFairuFile($id: ID!, $name: String!) {
            renameFairuFile(id: $id, name: $name) {
                id
                name
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id, 'name' => $name]);

        if (! isset($result['renameFairuFile'])) {
            return null;
        }

        return new Asset($result['renameFairuFile']);
    }

    public function move(string $id, ?string $parentId = null): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation MoveFairuFile($id: ID!, $parent: ID) {
            moveFairuFile(id: $id, parent: $parent)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id, 'parent' => $parentId]);

        return $result['moveFairuFile'] ?? false;
    }

    public function duplicate(string $id, ?string $parentId = null): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DuplicateFairuFile($id: ID!, $parent: ID) {
            duplicateFairuFile(id: $id, parent: $parent)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id, 'parent' => $parentId]);

        return $result['duplicateFairuFile'] ?? false;
    }

    public function redownload(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation RedownloadFairuFile($id: ID!) {
            redownloadFairuFile(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['redownloadFairuFile'] ?? false;
    }

    public function replace(string $id): UploadLink
    {
        $mutation = <<<'GRAPHQL'
        mutation ReplaceFairuFile($id: ID!) {
            replaceFairuFile(id: $id) {
                id
                url
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return new UploadLink($result['replaceFairuFile'] ?? []);
    }

    public function createAccessSignatures(array $ids, ?int $validForMinutes = null): array
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuFileAccessSignature($ids: [ID!]!, $valid_for_minutes: Int) {
            createFairuFileAccessSignature(ids: $ids, valid_for_minutes: $valid_for_minutes) {
                id
                signature
                expires_at
            }
        }
        GRAPHQL;

        $variables = array_filter([
            'ids' => $ids,
            'valid_for_minutes' => $validForMinutes,
        ], fn ($v) => $v !== null);

        $result = $this->executeMutation($mutation, $variables);

        return array_map(
            fn ($item) => new FileAccessSignature($item),
            $result['createFairuFileAccessSignature'] ?? []
        );
    }

    public function getUrlByPath(
        string $tenantId,
        string $path,
        ?int $width = null,
        ?int $height = null,
        ?string $focalPoint = null,
        bool $withStoredFocalPoint = false,
        ?int $quality = null,
        ?VideoVersions $version = null
    ): ?string {
        $mutation = <<<'GRAPHQL'
        mutation FairuFileUrlByPath(
            $tenant: ID!,
            $path: String!,
            $width: Int,
            $height: Int,
            $focal_point: String,
            $withStoredFocalPoint: Boolean,
            $quality: Int,
            $version: FairuAssetVideoVersions
        ) {
            fairuFileUrlByPath(
                tenant: $tenant,
                path: $path,
                width: $width,
                height: $height,
                focal_point: $focal_point,
                withStoredFocalPoint: $withStoredFocalPoint,
                quality: $quality,
                version: $version
            )
        }
        GRAPHQL;

        $variables = array_filter([
            'tenant' => $tenantId,
            'path' => $path,
            'width' => $width,
            'height' => $height,
            'focal_point' => $focalPoint,
            'withStoredFocalPoint' => $withStoredFocalPoint ?: null,
            'quality' => $quality,
            'version' => $version?->value,
        ], fn ($v) => $v !== null);

        $result = $this->executeMutation($mutation, $variables);

        return $result['fairuFileUrlByPath'] ?? null;
    }
}
