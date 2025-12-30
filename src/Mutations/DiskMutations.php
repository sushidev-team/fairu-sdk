<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\DiskDTO;
use SushiDev\Fairu\Responses\Disk;

class DiskMutations extends BaseMutation
{
    public function create(DiskDTO $data): ?Disk
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuDisk($data: FairuDiskDTO!) {
            createFairuDisk(data: $data) {
                id
                name
                type
                path
                pattern
                active
                healthy
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['createFairuDisk'])) {
            return null;
        }

        return new Disk($result['createFairuDisk']);
    }

    public function update(DiskDTO $data): ?Disk
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuDisk($data: FairuDiskDTO!) {
            updateFairuDisk(data: $data) {
                id
                name
                type
                path
                pattern
                active
                healthy
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['updateFairuDisk'])) {
            return null;
        }

        return new Disk($result['updateFairuDisk']);
    }

    public function delete(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuDisk($id: ID!) {
            deleteFairuDisk(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['deleteFairuDisk'] ?? false;
    }
}
