<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Enums;

/**
 * Controls what a pre-upload deduplication check does when an existing asset
 * with the same content fingerprint is found in the tenant.
 */
enum DuplicateStrategy: string
{
    /** Return the existing asset and skip the upload entirely. */
    case SKIP = 'skip';

    /** Ignore duplicates and always upload a new asset (no hashing performed). */
    case ALLOW = 'allow';

    /** Throw a DuplicateAssetException when a duplicate is found. */
    case FAIL = 'fail';

    /** Flag the duplicate but still upload a new asset. */
    case CHECK = 'check';
}
