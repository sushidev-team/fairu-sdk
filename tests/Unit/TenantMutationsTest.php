<?php

use SushiDev\Fairu\Exceptions\GraphQLException;
use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Mutations\TenantMutations;

it('deletes a sub-tenant through a parameterized mutation', function (array $response, bool $expected) {
    $client = Mockery::mock(FairuClient::class);
    $client->shouldReceive('mutate')->once()->withArgs(function (string $mutation, array $variables): bool {
        return str_contains($mutation, 'mutation DeleteFairuSubTenant($id: ID!)')
            && str_contains($mutation, 'deleteFairuSubTenant(id: $id)')
            && $variables === ['id' => 'sub-tenant-id'];
    })->andReturn($response);

    expect((new TenantMutations($client))->deleteSubTenant('sub-tenant-id'))->toBe($expected);
})->with([
    'deleted' => [['deleteFairuSubTenant' => true], true],
    'refused' => [['deleteFairuSubTenant' => false], false],
    'missing result' => [[], false],
]);

it('preserves GraphQL not-found and permission errors for callers', function (int $code) {
    $error = new GraphQLException([[
        'message' => $code === 404 ? 'Entry not found' : 'Missing permissions',
        'extensions' => ['code' => $code],
    ]]);
    $client = Mockery::mock(FairuClient::class);
    $client->shouldReceive('mutate')->once()->andThrow($error);

    try {
        (new TenantMutations($client))->deleteSubTenant('sub-tenant-id');
        test()->fail('Expected the GraphQL error to reach the caller.');
    } catch (GraphQLException $exception) {
        expect($exception)->toBe($error)
            ->and($exception->getFirstError()['extensions']['code'])->toBe($code);
    }
})->with([404, 403]);

afterEach(function () {
    Mockery::close();
});
