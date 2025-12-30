<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\RoleDTO;
use SushiDev\Fairu\Responses\Role;

class RoleMutations extends BaseMutation
{
    public function create(RoleDTO $data): ?Role
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuRole($data: FairuRoleDTO!) {
            createFairuRole(data: $data) {
                id
                name
                permissions
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['createFairuRole'])) {
            return null;
        }

        return new Role($result['createFairuRole']);
    }

    public function update(RoleDTO $data): ?Role
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuRole($data: FairuRoleDTO!) {
            updateFairuRole(data: $data) {
                id
                name
                permissions
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['updateFairuRole'])) {
            return null;
        }

        return new Role($result['updateFairuRole']);
    }

    public function delete(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuRole($id: ID!) {
            deleteFairuRole(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['deleteFairuRole'] ?? false;
    }
}
