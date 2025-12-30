<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\CopyrightDTO;
use SushiDev\Fairu\Responses\Copyright;

class CopyrightMutations extends BaseMutation
{
    public function create(CopyrightDTO $data): ?Copyright
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuCopyright($data: FairuCopyrightDTO!) {
            createFairuCopyright(data: $data) {
                id
                name
                email
                phone
                website
                active
                blocked
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['createFairuCopyright'])) {
            return null;
        }

        return new Copyright($result['createFairuCopyright']);
    }

    public function update(CopyrightDTO $data): ?Copyright
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuCopyright($data: FairuCopyrightDTO!) {
            updateFairuCopyright(data: $data) {
                id
                name
                email
                phone
                website
                active
                blocked
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['updateFairuCopyright'])) {
            return null;
        }

        return new Copyright($result['updateFairuCopyright']);
    }

    public function delete(string $id, bool $deleteAssets = false, bool $deleteLicenses = false): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuCopyright($id: ID!, $deleteAssets: Boolean, $deleteLicenses: Boolean) {
            deleteFairuCopyright(id: $id, deleteAssets: $deleteAssets, deleteLicenses: $deleteLicenses)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, [
            'id' => $id,
            'deleteAssets' => $deleteAssets,
            'deleteLicenses' => $deleteLicenses,
        ]);

        return $result['deleteFairuCopyright'] ?? false;
    }
}
