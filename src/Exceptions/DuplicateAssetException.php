<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Exceptions;

use SushiDev\Fairu\Responses\Asset;

/**
 * Thrown by a pre-upload deduplication check using DuplicateStrategy::FAIL when
 * an asset with the same content fingerprint already exists in the tenant.
 */
class DuplicateAssetException extends FairuException
{
    public function __construct(
        public readonly Asset $existingAsset,
        public readonly string $fingerprint,
    ) {
        parent::__construct(
            "A duplicate asset already exists for this content (existing asset id: {$existingAsset->getId()})."
        );
    }
}
