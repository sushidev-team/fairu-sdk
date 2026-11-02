<?php

use SushiDev\Fairu\FileProxy\FileProxyBuilder;
use SushiDev\Fairu\FileProxy\WatermarkSignature;

function watermarkVectors(): array
{
    return json_decode(file_get_contents(__DIR__.'/../fixtures/watermark-signature-vectors.json'), true, flags: JSON_THROW_ON_ERROR);
}

function watermarkVectorKey(int $version): string
{
    foreach (watermarkVectors()['keys'] as $key) {
        if ($key['key_version'] === $version) {
            return $key['display_key'];
        }
    }

    throw new RuntimeException("Unknown key version {$version}");
}

function watermarkQuery(string $url): array
{
    return WatermarkSignature::parseQuery((string) parse_url($url, PHP_URL_QUERY));
}

describe('WatermarkSignature', function () {
    it('decodes display keys to the raw key bytes', function () {
        foreach (watermarkVectors()['keys'] as $key) {
            expect(bin2hex(WatermarkSignature::rawKey($key['display_key'])))->toBe($key['raw_key_hex']);
        }
    });

    it('matches the shared signature vectors', function () {
        $vectors = watermarkVectors();

        foreach ($vectors['cases'] as $case) {
            $key = watermarkVectorKey($case['key_version']);

            expect(WatermarkSignature::canonical($vectors['file_id'], $case['query']))->toBe($case['canonical'], $case['name'])
                ->and(WatermarkSignature::sign($key, $vectors['file_id'], $case['query']))->toBe($case['wms'], $case['name']);
        }
    });

    it('rejects malformed display keys', function (string $key) {
        WatermarkSignature::rawKey($key);
    })->throws(InvalidArgumentException::class)->with([
        'missing prefix' => 'd9xh6ssIShqKkolIJk7TaARqO4vz3YEpaTZaoNAinTE',
        'empty' => 'fwm_',
        'not base64url' => 'fwm_d9xh6ssIShqKkolIJk7TaARq+4vz3YEpaTZaoNAinTE',
        'wrong length' => 'fwm_d9xh6ssIShqKkolIJk7TaA',
    ]);
});

describe('FileProxyBuilder::withoutWatermark', function () {
    it('signs the vector query', function () {
        $vectors = watermarkVectors();

        $url = FileProxyBuilder::make('https://files.fairu.app', $vectors['file_id'], 'image.jpg')
            ->withoutWatermark(watermarkVectorKey(1))
            ->width(1600)
            ->toUrl();

        expect($url)->toBe("https://files.fairu.app/{$vectors['file_id']}/image.jpg?width=1600&wm=0&wms=O70ZFBx6ylPOX70Y6Wwjk5");
    });

    it('signs parameters set after withoutWatermark', function () {
        $vectors = watermarkVectors();
        $key = watermarkVectorKey(1);

        $url = FileProxyBuilder::make('https://files.fairu.app', $vectors['file_id'], 'image.jpg')
            ->withoutWatermark($key)
            ->width(800)
            ->focal(50, 50, 1.0)
            ->webp()
            ->param('tint', '#00ff00')
            ->toUrl();

        $query = watermarkQuery($url);

        expect($query)->toHaveKeys(['width', 'focal', 'format', 'tint', 'wm', 'wms'])
            ->and($query['wm'])->toBe('0')
            ->and($query['wms'])->toBe(WatermarkSignature::sign($key, $vectors['file_id'], $query));
    });

    it('signs raw requests', function () {
        $vectors = watermarkVectors();

        $url = FileProxyBuilder::make('https://files.fairu.app', $vectors['file_id'], 'image.jpg')
            ->withoutWatermark(watermarkVectorKey(1))
            ->toUrl();

        expect($url)->toBe("https://files.fairu.app/{$vectors['file_id']}/image.jpg?wm=0&wms=j7lsodayymlxtSYwnPWD96");
    });

    it('replaces a custom wms parameter', function () {
        $vectors = watermarkVectors();

        $url = FileProxyBuilder::make('https://files.fairu.app', $vectors['file_id'], 'image.jpg')
            ->param('wms', 'forged')
            ->param('wm', '1')
            ->width(1600)
            ->withoutWatermark(watermarkVectorKey(1))
            ->toUrl();

        expect($url)->toBe("https://files.fairu.app/{$vectors['file_id']}/image.jpg?width=1600&wm=0&wms=O70ZFBx6ylPOX70Y6Wwjk5");
    });

    it('does not sign without withoutWatermark', function () {
        $url = FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')->width(800)->toUrl();

        expect($url)->toBe('https://files.fairu.app/abc-123/image.jpg?width=800');
    });

    it('rejects an invalid key immediately', function () {
        FileProxyBuilder::make('https://files.fairu.app', 'abc-123', 'image.jpg')->withoutWatermark('nope');
    })->throws(InvalidArgumentException::class);
});
