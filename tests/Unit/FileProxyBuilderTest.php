<?php

use SushiDev\Fairu\FileProxy\FileProxyBuilder;
use SushiDev\Fairu\Enums\FileProxyFit;
use SushiDev\Fairu\Enums\FileProxyFormat;
use SushiDev\Fairu\Enums\VideoVersions;

describe('FileProxyBuilder', function () {
    it('creates a basic URL', function () {
        $builder = new FileProxyBuilder('https://files.fairu.app', 'abc-123', 'image.jpg');

        expect($builder->toUrl())->toBe('https://files.fairu.app/abc-123/image.jpg');
    });

    it('can be created with static make method', function () {
        $builder = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg');

        expect($builder)->toBeInstanceOf(FileProxyBuilder::class);
        expect($builder->toUrl())->toBe('https://files.fairu.app/abc-123/image.jpg');
    });

    it('trims trailing slash from base URL', function () {
        $builder = new FileProxyBuilder('https://files.fairu.app/', 'abc-123', 'image.jpg');

        expect($builder->toUrl())->toBe('https://files.fairu.app/abc-123/image.jpg');
    });

    it('adds width parameter', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->width(800)
            ->toUrl();

        expect($url)->toBe('https://files.fairu.app/abc-123/image.jpg?width=800');
    });

    it('adds height parameter', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->height(600)
            ->toUrl();

        expect($url)->toBe('https://files.fairu.app/abc-123/image.jpg?height=600');
    });

    it('adds dimensions with both width and height', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->dimensions(800, 600)
            ->toUrl();

        expect($url)->toContain('width=800');
        expect($url)->toContain('height=600');
    });

    it('clamps width to valid range', function () {
        $builder = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg');

        expect($builder->width(0)->getParams()['width'])->toBe(1);
        expect($builder->width(10000)->getParams()['width'])->toBe(6000);
    });

    it('clamps height to valid range', function () {
        $builder = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg');

        expect($builder->height(0)->getParams()['height'])->toBe(1);
        expect($builder->height(10000)->getParams()['height'])->toBe(6000);
    });

    it('adds quality parameter', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->quality(85)
            ->toUrl();

        expect($url)->toContain('quality=85');
    });

    it('clamps quality to valid range', function () {
        $builder = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg');

        expect($builder->quality(0)->getParams()['quality'])->toBe(1);
        expect($builder->quality(150)->getParams()['quality'])->toBe(100);
    });

    it('adds format parameter with enum', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->format(FileProxyFormat::WEBP)
            ->toUrl();

        expect($url)->toContain('format=webp');
    });

    it('has shorthand format methods', function () {
        $base = 'https://files.fairu.app';

        expect(FileProxyBuilder::make($base, 'id', 'f')->jpg()->getParams()['format'])->toBe('jpg');
        expect(FileProxyBuilder::make($base, 'id', 'f')->png()->getParams()['format'])->toBe('png');
        expect(FileProxyBuilder::make($base, 'id', 'f')->webp()->getParams()['format'])->toBe('webp');
    });

    it('adds fit parameter with enum', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->fit(FileProxyFit::CONTAIN)
            ->toUrl();

        expect($url)->toContain('fit=contain');
    });

    it('has shorthand fit methods', function () {
        $base = 'https://files.fairu.app';

        expect(FileProxyBuilder::make($base, 'id', 'f')->cover()->getParams()['fit'])->toBe('cover');
        expect(FileProxyBuilder::make($base, 'id', 'f')->contain()->getParams()['fit'])->toBe('contain');
    });

    it('adds focal point without zoom', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->focal(50, 30)
            ->toUrl();

        expect($url)->toContain('focal=50-30');
    });

    it('adds focal point with zoom', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->focal(50, 50, 2.5)
            ->toUrl();

        expect($url)->toContain('focal=50-50-2.5');
    });

    it('clamps focal point values', function () {
        $builder = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->focal(-10, 150, 0.5);

        expect($builder->getParams()['focal'])->toBe('0-100-1');
    });

    it('adds raw parameter', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'file.eps')
            ->raw()
            ->toUrl();

        expect($url)->toContain('raw=true');
    });

    it('adds process_svg parameter', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'logo.svg')
            ->processSvg()
            ->toUrl();

        expect($url)->toContain('process_svg=true');
    });

    it('adds video version parameter', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'video.mp4')
            ->videoVersion(VideoVersions::HIGH)
            ->toUrl();

        expect($url)->toContain('version=HIGH');
    });

    it('adds timestamp parameter', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'video.mp4')
            ->timestamp('00:01:30.500')
            ->toUrl();

        expect($url)->toContain('timestamp=00%3A01%3A30.500');
    });

    it('adds signature parameters', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->signature('abc123signature', '2024-01-15')
            ->toUrl();

        expect($url)->toContain('signature=abc123signature');
        expect($url)->toContain('signature_date=2024-01-15');
    });

    it('adds custom parameters', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->param('custom', 'value')
            ->toUrl();

        expect($url)->toContain('custom=value');
    });

    it('chains multiple parameters', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->width(800)
            ->height(600)
            ->quality(85)
            ->webp()
            ->cover()
            ->toUrl();

        expect($url)->toContain('width=800');
        expect($url)->toContain('height=600');
        expect($url)->toContain('quality=85');
        expect($url)->toContain('format=webp');
        expect($url)->toContain('fit=cover');
    });

    it('can be cast to string', function () {
        $builder = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->width(400);

        expect((string) $builder)->toBe($builder->toUrl());
    });

    it('returns params array', function () {
        $builder = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')
            ->width(800)
            ->quality(90);

        $params = $builder->getParams();

        expect($params)->toBeArray();
        expect($params['width'])->toBe(800);
        expect($params['quality'])->toBe(90);
    });
});
