<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

class UserMutations extends BaseMutation
{
    public function invite(string $email, string $roleId): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation InviteFairuUser($email: String!, $role: ID!) {
            inviteFairuUser(email: $email, role: $role)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, [
            'email' => $email,
            'role' => $roleId,
        ]);

        return $result['inviteFairuUser'] ?? false;
    }

    public function delete(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuUser($id: ID!) {
            deleteFairuUser(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['deleteFairuUser'] ?? false;
    }
}
