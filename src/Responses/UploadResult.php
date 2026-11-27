<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * Outcome of a pre-upload deduplication check.
 *
 * - $isDuplicate     whether a matching asset already exists in the tenant
 * - $asset           the existing asset when a duplicate was found, else null
 * - $existingAssetId convenience id of the existing asset, else null
 * - $shouldUpload    whether the caller should proceed with the actual upload
 * - $fingerprint     the SHA1 computed for the local file (null for ALLOW)
 */
class UploadResult
{
    public function __construct(
        public readonly bool $isDuplicate,
        public readonly bool $shouldUpload,
        public readonly ?Asset $asset = null,
        public readonly ?string $existingAssetId = null,
        public readonly ?string $fingerprint = null,
    ) {}
}
