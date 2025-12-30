<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\Responses\RakuCredentialWithSecret;

class RakuMutations extends BaseMutation
{
    public function createCredential(
        ?string $name = null,
        ?string $bucket = null,
        array $permissions = []
    ): RakuCredentialWithSecret {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuRakuCredential($name: String, $bucket: String, $permissions: [String!]!) {
            createFairuRakuCredential(name: $name, bucket: $bucket, permissions: $permissions) {
                id
                name
                access_key_id
                secret_access_key
                bucket
                permissions
                active
                created_at
            }
        }
        GRAPHQL;

        $variables = array_filter([
            'name' => $name,
            'bucket' => $bucket,
            'permissions' => $permissions,
        ], fn ($v) => $v !== null && $v !== []);

        // Ensure permissions is always present
        $variables['permissions'] = $permissions;

        $result = $this->executeMutation($mutation, $variables);

        return new RakuCredentialWithSecret($result['createFairuRakuCredential'] ?? []);
    }

    public function revokeCredential(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation RevokeFairuRakuCredential($id: ID!) {
            revokeFairuRakuCredential(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['revokeFairuRakuCredential'] ?? false;
    }

    public function deleteCredential(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuRakuCredential($id: ID!) {
            deleteFairuRakuCredential(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['deleteFairuRakuCredential'] ?? false;
    }
}
