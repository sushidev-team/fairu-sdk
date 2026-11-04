<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Enums\DuplicateStrategy;
use SushiDev\Fairu\Enums\UploadType;
use SushiDev\Fairu\Exceptions\DuplicateAssetException;
use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Responses\MultipartUploadInit;
use SushiDev\Fairu\Responses\UploadLink;
use SushiDev\Fairu\Responses\UploadResult;
use SushiDev\Fairu\Support\FingerprintHasher;

class UploadMutations
{
    public function __construct(
        protected readonly FairuClient $client,
    ) {}

    /**
     * Deduplication check to run before uploading a local file.
     *
     * Hashes the file locally (SHA1 of its bytes, matching the server) and asks
     * the API whether an identical asset already exists in the tenant, so an
     * upload of duplicate bytes can be avoided entirely. The behaviour on a hit
     * is controlled by $onDuplicate:
     *
     *   SKIP  - return the existing asset, shouldUpload = false (default)
     *   CHECK - flag the duplicate but still upload (shouldUpload = true)
     *   FAIL  - throw a DuplicateAssetException
     *   ALLOW - never hash or query; always upload as new
     *
     * Typical usage:
     *
     *   $result = Fairu::uploads()->checkDuplicate($path, DuplicateStrategy::SKIP);
     *   if (! $result->shouldUpload) {
     *       return $result->asset; // existing asset, nothing transferred
     *   }
     *   // ...proceed with initMultipart()/part PUTs/completeMultipart()...
     *
     * @throws DuplicateAssetException when $onDuplicate is FAIL and a duplicate exists
     */
    public function checkDuplicate(
        string $filePath,
        DuplicateStrategy $onDuplicate = DuplicateStrategy::SKIP,
        ?FragmentInterface $fragment = null,
    ): UploadResult {
        if ($onDuplicate === DuplicateStrategy::ALLOW) {
            return new UploadResult(isDuplicate: false, shouldUpload: true);
        }

        $hasher = new FingerprintHasher;
        $fingerprint = $hasher->hashFile($filePath);

        if ($hasher->isEmptyFile($fingerprint)) {
            return new UploadResult(isDuplicate: false, shouldUpload: true, fingerprint: $fingerprint);
        }

        // Always query fresh: a just-uploaded duplicate must be visible, and a
        // cached "miss" would defeat the check.
        $existing = $this->client->assets()->fresh()->findByFingerprint($fingerprint, $fragment);

        if ($existing === null) {
            return new UploadResult(isDuplicate: false, shouldUpload: true, fingerprint: $fingerprint);
        }

        return match ($onDuplicate) {
            DuplicateStrategy::FAIL => throw new DuplicateAssetException($existing, $fingerprint),
            DuplicateStrategy::CHECK => new UploadResult(
                isDuplicate: true,
                shouldUpload: true,
                asset: $existing,
                existingAssetId: $existing->getId(),
                fingerprint: $fingerprint,
            ),
            default => new UploadResult(
                isDuplicate: true,
                shouldUpload: false,
                asset: $existing,
                existingAssetId: $existing->getId(),
                fingerprint: $fingerprint,
            ),
        };
    }

    public function createLink(
        string $filename,
        UploadType $type = UploadType::STANDARD,
        ?string $folderId = null,
        ?string $id = null,
        ?string $downloadUrl = null,
        ?string $alt = null,
        ?string $caption = null,
        ?string $description = null,
        ?string $focalPoint = null,
        ?string $copyright = null
    ): UploadLink {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuUploadLink(
            $type: FairuUploadType!,
            $filename: String!,
            $folder: ID,
            $id: ID,
            $download_url: String,
            $alt: String,
            $caption: String,
            $description: String,
            $focal_point: String,
            $copyright: String
        ) {
            createFairuUploadLink(
                type: $type,
                filename: $filename,
                folder: $folder,
                id: $id,
                download_url: $download_url,
                alt: $alt,
                caption: $caption,
                description: $description,
                focal_point: $focal_point,
                copyright: $copyright
            ) {
                id
                upload_url
            }
        }
        GRAPHQL;

        $variables = array_filter([
            'type' => $type->value,
            'filename' => $filename,
            'folder' => $folderId,
            'id' => $id,
            'download_url' => $downloadUrl,
            'alt' => $alt,
            'caption' => $caption,
            'description' => $description,
            'focal_point' => $focalPoint,
            'copyright' => $copyright,
        ], fn ($v) => $v !== null);

        $result = $this->client->mutate($mutation, $variables);

        return new UploadLink($result['createFairuUploadLink'] ?? []);
    }

    public function initMultipart(
        string $filename,
        ?string $folderId = null,
        ?int $fileSize = null,
        ?string $contentType = null,
        ?string $alt = null,
        ?string $caption = null,
        ?string $description = null,
        ?string $copyright = null
    ): MultipartUploadInit {
        $mutation = <<<'GRAPHQL'
        mutation InitFairuMultipartUpload(
            $filename: String!,
            $folder: ID,
            $fileSize: Int,
            $contentType: String,
            $alt: String,
            $caption: String,
            $description: String,
            $copyright: String
        ) {
            initFairuMultipartUpload(
                filename: $filename,
                folder: $folder,
                fileSize: $fileSize,
                contentType: $contentType,
                alt: $alt,
                caption: $caption,
                description: $description,
                copyright: $copyright
            ) {
                id
                uploadId
            }
        }
        GRAPHQL;

        $variables = array_filter([
            'filename' => $filename,
            'folder' => $folderId,
            'fileSize' => $fileSize,
            'contentType' => $contentType,
            'alt' => $alt,
            'caption' => $caption,
            'description' => $description,
            'copyright' => $copyright,
        ], fn ($v) => $v !== null);

        $result = $this->client->mutate($mutation, $variables);

        return new MultipartUploadInit($result['initFairuMultipartUpload'] ?? []);
    }

    public function getMultipartPartUrl(string $fileId, string $uploadId, int $partNumber): array
    {
        $mutation = <<<'GRAPHQL'
        mutation GetFairuMultipartPartUrl($fileId: ID!, $uploadId: String!, $partNumber: Int!) {
            getFairuMultipartPartUrl(fileId: $fileId, uploadId: $uploadId, partNumber: $partNumber) {
                uploadUrl
                partNumber
            }
        }
        GRAPHQL;

        $result = $this->client->mutate($mutation, [
            'fileId' => $fileId,
            'uploadId' => $uploadId,
            'partNumber' => $partNumber,
        ]);

        return $result['getFairuMultipartPartUrl'] ?? [];
    }

    public function completeMultipart(string $fileId, string $uploadId, array $parts): UploadLink
    {
        $mutation = <<<'GRAPHQL'
        mutation CompleteFairuMultipartUpload($fileId: ID!, $uploadId: String!, $parts: [FairuMultipartPartInput!]!) {
            completeFairuMultipartUpload(fileId: $fileId, uploadId: $uploadId, parts: $parts) {
                id
                upload_url
            }
        }
        GRAPHQL;

        $result = $this->client->mutate($mutation, [
            'fileId' => $fileId,
            'uploadId' => $uploadId,
            'parts' => $parts,
        ]);

        return new UploadLink($result['completeFairuMultipartUpload'] ?? []);
    }

    public function abortMultipart(string $fileId, string $uploadId): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation AbortFairuMultipartUpload($fileId: ID!, $uploadId: String!) {
            abortFairuMultipartUpload(fileId: $fileId, uploadId: $uploadId)
        }
        GRAPHQL;

        $result = $this->client->mutate($mutation, [
            'fileId' => $fileId,
            'uploadId' => $uploadId,
        ]);

        return $result['abortFairuMultipartUpload'] ?? false;
    }
}
