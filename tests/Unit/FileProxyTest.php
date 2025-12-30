<?php

use SushiDev\Fairu\FileProxy\FileProxy;
use SushiDev\Fairu\FileProxy\FileProxyBuilder;
use SushiDev\Fairu\Responses\Asset;

describe('FileProxy', function () {
    it('creates instance with base URL', function () {
        $proxy = new FileProxy('https://files.fairu.app');

        expect($proxy->getBaseUrl())->toBe('https://files.fairu.app');
    });

    it('trims trailing slash from base URL', function () {
        $proxy = new FileProxy('https://files.fairu.app/');

        expect($proxy->getBaseUrl())->toBe('https://files.fairu.app');
    });

    it('returns FileProxyBuilder from url method', function () {
        $proxy = new FileProxy('https://files.fairu.app');
        $builder = $proxy->url('abc-123', 'image.jpg');

        expect($builder)->toBeInstanceOf(FileProxyBuilder::class);
        expect($builder->toUrl())->toBe('https://files.fairu.app/abc-123/image.jpg');
    });

    it('uses default filename if not provided', function () {
        $proxy = new FileProxy('https://files.fairu.app');
        $builder = $proxy->url('abc-123');

        expect($builder->toUrl())->toBe('https://files.fairu.app/abc-123/file');
    });

    it('creates builder from Asset object', function () {
        $proxy = new FileProxy('https://files.fairu.app');

        $asset = new Asset([
            'id' => 'asset-uuid-123',
            'name' => 'photo.jpg',
            'mime' => 'image/jpeg',
        ]);

        $builder = $proxy->fromAsset($asset);

        expect($builder)->toBeInstanceOf(FileProxyBuilder::class);
        expect($builder->toUrl())->toBe('https://files.fairu.app/asset-uuid-123/photo.jpg');
    });

    it('handles Asset without name', function () {
        $proxy = new FileProxy('https://files.fairu.app');

        $asset = new Asset([
            'id' => 'asset-uuid-123',
            'mime' => 'image/jpeg',
        ]);

        $builder = $proxy->fromAsset($asset);

        expect($builder->toUrl())->toBe('https://files.fairu.app/asset-uuid-123/file');
    });

    it('generates HLS URL', function () {
        $proxy = new FileProxy('https://files.fairu.app');

        $url = $proxy->hlsUrl('tenant-123', 'asset-456');

        expect($url)->toBe('https://files.fairu.app/hls/tenant-123/asset-456/master.m3u8');
    });

    it('generates HLS URL with custom path', function () {
        $proxy = new FileProxy('https://files.fairu.app');

        $url = $proxy->hlsUrl('tenant-123', 'asset-456', '720p/playlist.m3u8');

        expect($url)->toBe('https://files.fairu.app/hls/tenant-123/asset-456/720p/playlist.m3u8');
    });

    it('allows chaining transformations from url', function () {
        $proxy = new FileProxy('https://files.fairu.app');

        $url = $proxy->url('abc-123', 'image.jpg')
            ->width(800)
            ->height(600)
            ->webp()
            ->quality(85)
            ->toUrl();

        expect($url)->toContain('width=800');
        expect($url)->toContain('height=600');
        expect($url)->toContain('format=webp');
        expect($url)->toContain('quality=85');
    });
});
