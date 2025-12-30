<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read string|null $ftp_host
 * @property-read int|null $ftp_port
 * @property-read string|null $ftp_username
 * @property-read string|null $key
 * @property-read string|null $region
 * @property-read string|null $bucket
 * @property-read string|null $endpoint
 * @property-read string|null $url
 */
class DiskCredentials extends BaseResponse
{
    public function getFtpHost(): ?string
    {
        return $this->data['ftp_host'] ?? null;
    }

    public function getFtpPort(): ?int
    {
        return $this->data['ftp_port'] ?? null;
    }

    public function getFtpUsername(): ?string
    {
        return $this->data['ftp_username'] ?? null;
    }

    public function getS3Key(): ?string
    {
        return $this->data['key'] ?? null;
    }

    public function getS3Region(): ?string
    {
        return $this->data['region'] ?? null;
    }

    public function getS3Bucket(): ?string
    {
        return $this->data['bucket'] ?? null;
    }

    public function getS3Endpoint(): ?string
    {
        return $this->data['endpoint'] ?? null;
    }
}
