<?php

declare(strict_types=1);

use SushiDev\Fairu\Support\FingerprintHasher;

/**
 * The fingerprint MUST stay byte-for-byte identical to the server algorithm in
 * fairu-app (App\Jobs\SyncFileFingerprint), which is a SHA1 of the raw file
 * bytes. If this drifts, deduplication silently stops matching.
 */
function tmpFileWith(string $contents): string
{
    $path = tempnam(sys_get_temp_dir(), 'fairu-fp-');
    file_put_contents($path, $contents);

    return $path;
}

it('hashes file contents as a plain SHA1 of the bytes (server parity)', function () {
    $contents = 'the quick brown fox';
    $path = tmpFileWith($contents);

    $hasher = new FingerprintHasher;

    expect($hasher->hashFile($path))
        ->toBe(sha1($contents))
        ->toHaveLength(40);

    unlink($path);
});

it('matches the streaming hash the server computes in 8KB chunks', function () {
    // Larger-than-one-chunk payload to mirror the server's streaming read.
    $contents = str_repeat('fairu-asset-bytes-', 2000); // ~36KB
    $path = tmpFileWith($contents);

    $hashContext = hash_init('sha1');
    $stream = fopen($path, 'rb');
    while (! feof($stream)) {
        hash_update($hashContext, fread($stream, 8192));
    }
    fclose($stream);
    $serverEquivalent = hash_final($hashContext);

    expect((new FingerprintHasher)->hashFile($path))->toBe($serverEquivalent);

    unlink($path);
});

it('produces the server SVG fingerprint for .svg bytes', function () {
    $svg = '<svg xmlns="http://www.w3.org/2000/svg"><rect width="1" height="1"/></svg>';
    $path = tmpFileWith($svg);

    // Server SVG branch: sha1(Storage::get($path)).
    expect((new FingerprintHasher)->hashFile($path))->toBe(sha1($svg));

    unlink($path);
});

it('hashes raw contents identically to a file', function () {
    $contents = 'inline-bytes';
    $path = tmpFileWith($contents);

    $hasher = new FingerprintHasher;
    expect($hasher->hashContents($contents))->toBe($hasher->hashFile($path));

    unlink($path);
});

it('detects the empty-file fingerprint', function () {
    $path = tmpFileWith('');
    $hasher = new FingerprintHasher;

    expect($hasher->hashFile($path))->toBe(FingerprintHasher::EMPTY_FILE_FINGERPRINT);
    expect($hasher->isEmptyFile($hasher->hashFile($path)))->toBeTrue();
    expect($hasher->isEmptyFile(sha1('not empty')))->toBeFalse();

    unlink($path);
});

it('throws for a missing file', function () {
    (new FingerprintHasher)->hashFile('/no/such/file/here.bin');
})->throws(InvalidArgumentException::class);
