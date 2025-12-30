<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\WorkflowDTO;
use SushiDev\Fairu\Responses\Workflow;

class WorkflowMutations extends BaseMutation
{
    public function create(WorkflowDTO $data): ?Workflow
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuWorkflow($data: FairuWorkflowDTO!) {
            createFairuWorkflow(data: $data) {
                id
                name
                type
                active
                status
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['createFairuWorkflow'])) {
            return null;
        }

        return new Workflow($result['createFairuWorkflow']);
    }

    public function update(WorkflowDTO $data): ?Workflow
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuWorkflow($data: FairuWorkflowDTO!) {
            updateFairuWorkflow(data: $data) {
                id
                name
                type
                active
                status
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['updateFairuWorkflow'])) {
            return null;
        }

        return new Workflow($result['updateFairuWorkflow']);
    }

    public function delete(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuWorkflow($id: ID!) {
            deleteFairuWorkflow(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['deleteFairuWorkflow'] ?? false;
    }
}
