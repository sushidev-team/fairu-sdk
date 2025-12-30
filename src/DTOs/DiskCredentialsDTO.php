<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

class DiskCredentialsDTO extends BaseDTO
{
    // FTP/SFTP credentials
    public function ftpHost(string $host): self
    {
        $this->data['ftp_host'] = $host;

        return $this;
    }

    public function ftpPort(int $port): self
    {
        $this->data['ftp_port'] = $port;

        return $this;
    }

    public function ftpUsername(string $username): self
    {
        $this->data['ftp_username'] = $username;

        return $this;
    }

    public function ftpPassword(string $password): self
    {
        $this->data['ftp_password'] = $password;

        return $this;
    }

    // S3 credentials
    public function key(string $key): self
    {
        $this->data['key'] = $key;

        return $this;
    }

    public function secret(string $secret): self
    {
        $this->data['secret'] = $secret;

        return $this;
    }

    public function region(string $region): self
    {
        $this->data['region'] = $region;

        return $this;
    }

    public function bucket(string $bucket): self
    {
        $this->data['bucket'] = $bucket;

        return $this;
    }

    public function endpoint(?string $endpoint): self
    {
        $this->data['endpoint'] = $endpoint;

        return $this;
    }

    public function url(?string $url): self
    {
        $this->data['url'] = $url;

        return $this;
    }
}
