<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\Enums\UploadType;
use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Responses\MultipartUploadInit;
use SushiDev\Fairu\Responses\UploadLink;

class UploadMutations
{
    public function __construct(
        protected readonly FairuClient $client,
    ) {}

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
