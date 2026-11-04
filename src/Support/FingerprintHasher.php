<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Support;

use InvalidArgumentException;
use RuntimeException;

/**
 * Computes content fingerprints that match the server-side algorithm used by
 * fairu-app (App\Jobs\SyncFileFingerprint): a SHA1 hash of the file's raw
 * bytes. Streaming and whole-file hashing yield the identical SHA1, and the
 * server's ".svg" special case computes the same value, so a plain SHA1 of the
 * bytes is byte-for-byte compatible with the stored fingerprint.
 */
class FingerprintHasher
{
    /**
     * SHA1 of a zero-byte file. The server excludes this from deduplication
     * because every empty file would otherwise be a "duplicate".
     */
    public const EMPTY_FILE_FINGERPRINT = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';

    public function hashFile(string $path): string
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException("File not found or not readable: {$path}");
        }

        $hash = sha1_file($path);

        if ($hash === false) {
            throw new RuntimeException("Failed to compute fingerprint for: {$path}");
        }

        return $hash;
    }

    public function hashContents(string $contents): string
    {
        return sha1($contents);
    }

    public function isEmptyFile(string $fingerprint): bool
    {
        return $fingerprint === self::EMPTY_FILE_FINGERPRINT;
    }
}
