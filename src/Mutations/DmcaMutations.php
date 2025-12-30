<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\DmcaComplainDTO;
use SushiDev\Fairu\DTOs\DmcaDTO;
use SushiDev\Fairu\Responses\Dmca;

class DmcaMutations extends BaseMutation
{
    public function createComplain(DmcaComplainDTO $data): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuDmcaComplain($data: FairuDmcaComplainDTO!) {
            createFairuDmcaComplain(data: $data)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        return $result['createFairuDmcaComplain'] ?? false;
    }

    public function updateComplain(DmcaDTO $data): ?Dmca
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuDmcaComplain($data: FairuDmcaDTO!) {
            updateFairuDmcaComplain(data: $data) {
                id
                name
                email
                status
                reply
                reply_send
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['updateFairuDmcaComplain'])) {
            return null;
        }

        return new Dmca($result['updateFairuDmcaComplain']);
    }
}
