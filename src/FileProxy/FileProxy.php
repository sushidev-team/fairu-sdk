<?php

namespace SushiDev\Fairu\FileProxy;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use SushiDev\Fairu\Responses\Asset;

class FileProxy
{
    protected string $baseUrl;
    protected ?Client $client = null;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Create a URL builder for the given asset ID.
     */
    public function url(string $id, string $filename = 'file'): FileProxyBuilder
    {
        return new FileProxyBuilder($this->baseUrl, $id, $filename);
    }

    /**
     * Create a URL builder from an Asset response object.
     */
    public function fromAsset(Asset $asset): FileProxyBuilder
    {
        return new FileProxyBuilder(
            $this->baseUrl,
            $asset->id,
            $asset->name ?? 'file'
        );
    }

    /**
     * Get the metadata (dimensions) for an image.
     *
     * @return array{width: int, height: int}|null
     */
    public function meta(string $id): ?array
    {
        try {
            $response = $this->getClient()->get("/meta/{$id}");
            $data = json_decode($response->getBody()->getContents(), true);

            return $data;
        } catch (GuzzleException) {
            return null;
        }
    }

    /**
     * Check if the file exists (HEAD request).
     */
    public function exists(string $id, string $filename = 'file'): bool
    {
        try {
            $response = $this->getClient()->head("/{$id}/{$filename}");

            return $response->getStatusCode() === 200;
        } catch (GuzzleException) {
            return false;
        }
    }

    /**
     * Get the HLS streaming URL for a video.
     */
    public function hlsUrl(string $tenantId, string $assetId, string $path = 'master.m3u8'): string
    {
        return "{$this->baseUrl}/hls/{$tenantId}/{$assetId}/{$path}";
    }

    /**
     * Check the health status of the file proxy service.
     */
    public function health(): bool
    {
        try {
            $response = $this->getClient()->get('/health');

            return $response->getStatusCode() === 200;
        } catch (GuzzleException) {
            return false;
        }
    }

    /**
     * Get the base URL of the file proxy.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    protected function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = new Client([
                'base_uri' => $this->baseUrl,
                'timeout' => 10,
            ]);
        }

        return $this->client;
    }
}
