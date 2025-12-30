<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

function createMockClient(array $responses): Client
{
    $mock = new MockHandler(
        array_map(fn ($body) => new Response(200, [], json_encode($body)), $responses)
    );

    return new Client(['handler' => HandlerStack::create($mock)]);
}

function graphqlResponse(array $data): array
{
    return ['data' => $data];
}

function graphqlError(string $message, array $extensions = []): array
{
    return [
        'errors' => [
            [
                'message' => $message,
                'extensions' => $extensions,
            ],
        ],
    ];
}
