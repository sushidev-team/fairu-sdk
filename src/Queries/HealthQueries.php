<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Responses\HealthStatus;

class HealthQueries
{
    public function __construct(
        protected readonly FairuClient $client,
    ) {}

    public function check(): HealthStatus
    {
        $query = <<<'GRAPHQL'
        query {
            fairuHealthCheck {
                healthy
            }
        }
        GRAPHQL;

        $result = $this->client->query($query);

        return new HealthStatus($result['fairuHealthCheck'] ?? []);
    }

    public function supportedDomains(): array
    {
        $query = <<<'GRAPHQL'
        query {
            fairuSupportedDomains
        }
        GRAPHQL;

        $result = $this->client->query($query);

        return $result['fairuSupportedDomains'] ?? [];
    }
}
