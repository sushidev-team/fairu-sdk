<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use SushiDev\Fairu\Enums\DuplicateStrategy;
use SushiDev\Fairu\Exceptions\DuplicateAssetException;
use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Facades\Fairu;

/**
 * Swap the FairuClient's internal Guzzle client with a mock handler.
 *
 * @param  array<int, array<string, mixed>>  $responses
 * @param  array<int, array<string, mixed>>  $history
 */
function bindDedupeHttpClient(array $responses, array &$history): void
{
    $history = [];
    $mock = new MockHandler(array_map(
        fn (array $body): Response => new Response(200, [], json_encode($body)),
        $responses,
    ));

    $stack = HandlerStack::create($mock);
    $stack->push(Middleware::history($history));

    $client = new Client(['base_uri' => 'https://fairu.test/graphql', 'handler' => $stack]);

    $fairu = app(FairuClient::class);
    $reflection = new ReflectionClass($fairu);
    $property = $reflection->getProperty('httpClient');
    $property->setAccessible(true);
    $property->setValue($fairu, $client);
}

function tmpUploadFile(string $contents = 'duplicate-me'): string
{
    $path = tempnam(sys_get_temp_dir(), 'fairu-upload-');
    file_put_contents($path, $contents);

    return $path;
}

function fingerprintMatchResponse(string $id = 'existing-asset-1'): array
{
    return ['data' => ['fairuFileByFingerprint' => ['id' => $id, 'name' => 'original.jpg']]];
}

function fingerprintMissResponse(): array
{
    return ['data' => ['fairuFileByFingerprint' => null]];
}

it('SKIP returns the existing asset and signals no upload', function () {
    $history = [];
    bindDedupeHttpClient([fingerprintMatchResponse('asset-skip')], $history);
    $path = tmpUploadFile();

    $result = Fairu::uploads()->checkDuplicate($path, DuplicateStrategy::SKIP);

    expect($result->isDuplicate)->toBeTrue();
    expect($result->shouldUpload)->toBeFalse();
    expect($result->existingAssetId)->toBe('asset-skip');
    expect($result->asset?->getId())->toBe('asset-skip');
    expect($result->fingerprint)->toBe(sha1('duplicate-me'));
    expect($history)->toHaveCount(1);

    unlink($path);
});

it('SKIP proceeds with upload when no duplicate exists', function () {
    $history = [];
    bindDedupeHttpClient([fingerprintMissResponse()], $history);
    $path = tmpUploadFile('unique-content');

    $result = Fairu::uploads()->checkDuplicate($path, DuplicateStrategy::SKIP);

    expect($result->isDuplicate)->toBeFalse();
    expect($result->shouldUpload)->toBeTrue();
    expect($result->asset)->toBeNull();
    expect($result->fingerprint)->toBe(sha1('unique-content'));

    unlink($path);
});

it('CHECK flags the duplicate but still allows upload', function () {
    $history = [];
    bindDedupeHttpClient([fingerprintMatchResponse('asset-check')], $history);
    $path = tmpUploadFile();

    $result = Fairu::uploads()->checkDuplicate($path, DuplicateStrategy::CHECK);

    expect($result->isDuplicate)->toBeTrue();
    expect($result->shouldUpload)->toBeTrue();
    expect($result->existingAssetId)->toBe('asset-check');

    unlink($path);
});

it('FAIL throws a DuplicateAssetException carrying the existing asset', function () {
    $history = [];
    bindDedupeHttpClient([fingerprintMatchResponse('asset-fail')], $history);
    $path = tmpUploadFile();

    try {
        Fairu::uploads()->checkDuplicate($path, DuplicateStrategy::FAIL);
        $this->fail('Expected DuplicateAssetException was not thrown');
    } catch (DuplicateAssetException $e) {
        expect($e->existingAsset->getId())->toBe('asset-fail');
        expect($e->fingerprint)->toBe(sha1('duplicate-me'));
    }

    unlink($path);
});

it('ALLOW never hashes or queries and always uploads', function () {
    $history = [];
    bindDedupeHttpClient([], $history); // no HTTP responses queued
    $path = tmpUploadFile();

    $result = Fairu::uploads()->checkDuplicate($path, DuplicateStrategy::ALLOW);

    expect($result->isDuplicate)->toBeFalse();
    expect($result->shouldUpload)->toBeTrue();
    expect($result->fingerprint)->toBeNull();
    expect($history)->toHaveCount(0);

    unlink($path);
});

it('never deduplicates an empty file', function () {
    $history = [];
    bindDedupeHttpClient([], $history); // no query expected for empty files
    $path = tmpUploadFile('');

    $result = Fairu::uploads()->checkDuplicate($path, DuplicateStrategy::SKIP);

    expect($result->isDuplicate)->toBeFalse();
    expect($result->shouldUpload)->toBeTrue();
    expect($history)->toHaveCount(0);

    unlink($path);
});
