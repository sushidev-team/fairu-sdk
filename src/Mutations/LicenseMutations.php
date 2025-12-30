<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\LicenseDTO;
use SushiDev\Fairu\Responses\License;

class LicenseMutations extends BaseMutation
{
    public function create(LicenseDTO $data): ?License
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuLicense($data: FairuLicenseDTO!) {
            createFairuLicense(data: $data) {
                id
                name
                type
                active
                default
                start
                end
                interval
                days
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['createFairuLicense'])) {
            return null;
        }

        return new License($result['createFairuLicense']);
    }

    public function update(LicenseDTO $data): ?License
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuLicense($data: FairuLicenseDTO!) {
            updateFairuLicense(data: $data) {
                id
                name
                type
                active
                default
                start
                end
                interval
                days
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['updateFairuLicense'])) {
            return null;
        }

        return new License($result['updateFairuLicense']);
    }

    public function delete(string $id, bool $deleteAssets = false): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuLicense($id: ID!, $deleteAssets: Boolean) {
            deleteFairuLicense(id: $id, deleteAssets: $deleteAssets)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, [
            'id' => $id,
            'deleteAssets' => $deleteAssets,
        ]);

        return $result['deleteFairuLicense'] ?? false;
    }
}
