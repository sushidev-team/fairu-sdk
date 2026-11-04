<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Tests\TestCase;

uses(TestCase::class)->in('Feature');
uses()->in('Unit');

/**
 * Swap the internal Guzzle client on the resolved FairuClient singleton with
 * a mock handler so we can exercise the GraphQL wire format for new sub-tenant
 * queries and mutations. The caller receives request history by reference via
 * the $history out-parameter.
 *
 * @param  array<int, array<string, mixed>>  $responses
 * @param  array<int, array<string, mixed>>  $history
 */
function bindMockHttpClient(array $responses, array &$history): FairuClient
{
    $history = [];
    $historyMiddleware = \GuzzleHttp\Middleware::history($history);

    $mock = new MockHandler(array_map(
        fn (array $body): Response => new Response(200, [], json_encode($body)),
        $responses,
    ));

    $stack = HandlerStack::create($mock);
    $stack->push($historyMiddleware);

    $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://fairu.test/graphql',
        'handler' => $stack,
    ]);

    $fairu = app(FairuClient::class);
    $reflection = new ReflectionClass($fairu);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($fairu, $client);

    return $fairu;
}
