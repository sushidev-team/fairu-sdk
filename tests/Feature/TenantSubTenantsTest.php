<?php

declare(strict_types=1);

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use SushiDev\Fairu\Facades\Fairu;
use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Responses\Tenant;
use SushiDev\Fairu\Responses\TenantCreationResult;

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

describe('TenantMutations::createSubTenant', function () {
    it('sends the CreateFairuSubTenant mutation and hydrates the result', function () {
        $history = [];
        bindMockHttpClient([
            ['data' => [
                'createFairuSubTenant' => [
                    'id' => 'sub-uuid-1',
                    'name' => 'Brand A',
                    'api_key' => 'secret-api-key',
                    'created_at' => '2026-04-11T10:00:00+00:00',
                ],
            ]],
        ], $history);

        $result = Fairu::tenantMutations()->createSubTenant('Brand A');

        expect($result)->toBeInstanceOf(TenantCreationResult::class);
        expect($result->getId())->toBe('sub-uuid-1');
        expect($result->getName())->toBe('Brand A');
        expect($result->getApiKey())->toBe('secret-api-key');
        expect($result->getCreatedAt())->toBe('2026-04-11T10:00:00+00:00');

        expect($history)->toHaveCount(1);
        $body = json_decode((string) $history[0]['request']->getBody(), true);
        expect($body['query'])->toContain('createFairuSubTenant');
        expect($body['variables'])->toBe(['name' => 'Brand A']);
    });

    it('returns an empty TenantCreationResult when the payload is missing', function () {
        $history = [];
        bindMockHttpClient([
            ['data' => []],
        ], $history);

        $result = Fairu::tenantMutations()->createSubTenant('Ghost');

        expect($result)->toBeInstanceOf(TenantCreationResult::class);
        expect($result->getId())->toBeNull();
        expect($result->getApiKey())->toBeNull();
    });
});

describe('TenantMutations::detachSubTenant', function () {
    it('sends the DetachFairuSubTenant mutation and returns the tenant', function () {
        $history = [];
        bindMockHttpClient([
            ['data' => [
                'detachFairuSubTenant' => [
                    'id' => 'sub-uuid-1',
                    'name' => 'Brand A',
                    'parent_id' => null,
                    'is_sub_tenant' => false,
                ],
            ]],
        ], $history);

        $tenant = Fairu::tenantMutations()->detachSubTenant('sub-uuid-1');

        expect($tenant)->toBeInstanceOf(Tenant::class);
        expect($tenant->getId())->toBe('sub-uuid-1');
        expect($tenant->isSubTenant())->toBeFalse();
        expect($tenant->getParentId())->toBeNull();

        $body = json_decode((string) $history[0]['request']->getBody(), true);
        expect($body['query'])->toContain('detachFairuSubTenant');
        expect($body['variables'])->toBe(['id' => 'sub-uuid-1']);
    });

    it('returns null when the mutation response is empty', function () {
        $history = [];
        bindMockHttpClient([
            ['data' => []],
        ], $history);

        $tenant = Fairu::tenantMutations()->detachSubTenant('missing');

        expect($tenant)->toBeNull();
    });
});

describe('TenantQueries::subTenants', function () {
    it('sends fairuSubTenants query and hydrates an array of Tenant objects', function () {
        $history = [];
        bindMockHttpClient([
            ['data' => [
                'fairuSubTenants' => [
                    [
                        'id' => 'child-1',
                        'name' => 'Child 1',
                        'parent_id' => 'root',
                        'is_sub_tenant' => true,
                    ],
                    [
                        'id' => 'child-2',
                        'name' => 'Child 2',
                        'parent_id' => 'root',
                        'is_sub_tenant' => true,
                    ],
                ],
            ]],
        ], $history);

        $subTenants = Fairu::tenant()->subTenants();

        expect($subTenants)->toHaveCount(2);
        expect($subTenants[0])->toBeInstanceOf(Tenant::class);
        expect($subTenants[0]->getId())->toBe('child-1');
        expect($subTenants[0]->isSubTenant())->toBeTrue();
        expect($subTenants[1]->getParentId())->toBe('root');

        $body = json_decode((string) $history[0]['request']->getBody(), true);
        expect($body['query'])->toContain('fairuSubTenants');
        expect($body['query'])->toContain('parent_id');
        expect($body['query'])->toContain('is_sub_tenant');
    });

    it('returns an empty array when the response contains no sub-tenants', function () {
        $history = [];
        bindMockHttpClient([
            ['data' => ['fairuSubTenants' => []]],
        ], $history);

        expect(Fairu::tenant()->subTenants())->toBe([]);
    });

    it('accepts a custom fragment', function () {
        $history = [];
        bindMockHttpClient([
            ['data' => ['fairuSubTenants' => []]],
        ], $history);

        $fragment = \SushiDev\Fairu\Fragments\Predefined\TenantFragments::minimal();

        Fairu::tenant()->subTenants($fragment);

        $body = json_decode((string) $history[0]['request']->getBody(), true);
        expect($body['query'])->toContain('fairuSubTenants');
    });
});
