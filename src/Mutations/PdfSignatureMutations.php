<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\PdfSignatureRequestDTO;
use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Responses\PdfSignatureRequest;

class PdfSignatureMutations
{
    public function __construct(
        protected readonly FairuClient $client,
    ) {}

    public function create(PdfSignatureRequestDTO $data): ?PdfSignatureRequest
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuPdfSignatureRequest($data: FairuFilePdfSignatureRequestDTO!) {
            createFairuPdfSignatureRequest(data: $data) {
                id
                status
                emails
                config_url
                signature_id
            }
        }
        GRAPHQL;

        $result = $this->client->mutate($mutation, ['data' => $data->toArray()]);

        if (! isset($result['createFairuPdfSignatureRequest'])) {
            return null;
        }

        return new PdfSignatureRequest($result['createFairuPdfSignatureRequest']);
    }

    public function start(string $id): ?PdfSignatureRequest
    {
        $mutation = <<<'GRAPHQL'
        mutation StartFairuPdfSignatureRequest($id: ID!) {
            startFairuPdfSignatureRequest(id: $id) {
                id
                status
                config_url
            }
        }
        GRAPHQL;

        $result = $this->client->mutate($mutation, ['id' => $id]);

        if (! isset($result['startFairuPdfSignatureRequest'])) {
            return null;
        }

        return new PdfSignatureRequest($result['startFairuPdfSignatureRequest']);
    }

    public function cancel(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation CancelFairuPdfSignatureRequest($id: ID!) {
            cancelFairuPdfSignatureRequest(id: $id)
        }
        GRAPHQL;

        $result = $this->client->mutate($mutation, ['id' => $id]);

        return $result['cancelFairuPdfSignatureRequest'] ?? false;
    }
}
