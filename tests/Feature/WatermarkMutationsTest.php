<?php

declare(strict_types=1);

use SushiDev\Fairu\DTOs\FileDTO;
use SushiDev\Fairu\DTOs\FolderDTO;
use SushiDev\Fairu\Facades\Fairu;

function sentVariables(array $history): array
{
    return json_decode((string) $history[0]['request']->getBody(), true)['variables'];
}

describe('AssetMutations::setWatermark', function () {
    it('sends the ids and value and returns the count', function () {
        $history = [];
        bindMockHttpClient([['data' => ['setFairuWatermark' => 2]]], $history);

        $count = Fairu::assetMutations()->setWatermark(['a', 'b'], true);

        $body = json_decode((string) $history[0]['request']->getBody(), true);

        expect($count)->toBe(2)
            ->and($body['query'])->toContain('setFairuWatermark(ids: $ids, value: $value)')
            ->and($body['variables'])->toBe(['ids' => ['a', 'b'], 'value' => true]);
    });

    it('sends an explicit null to inherit', function () {
        $history = [];
        bindMockHttpClient([['data' => ['setFairuWatermark' => 1]]], $history);

        Fairu::assetMutations()->setWatermark(['a'], null);

        expect(sentVariables($history))->toBe(['ids' => ['a'], 'value' => null]);
    });
});

describe('watermark on update DTOs', function () {
    it('sends an explicit null on a file update', function () {
        $history = [];
        bindMockHttpClient([['data' => ['updateFairuFile' => ['id' => 'a']]]], $history);

        Fairu::assetMutations()->update(FileDTO::make()->id('a')->watermark(null));

        expect(sentVariables($history)['data'])->toBe(['id' => 'a', 'watermark' => null]);
    });

    it('sends an explicit null on a folder update', function () {
        $history = [];
        bindMockHttpClient([['data' => ['updateFairuFolder' => ['id' => 'f']]]], $history);

        Fairu::folderMutations()->update(FolderDTO::make()->id('f')->watermark(null));

        expect(sentVariables($history)['data'])->toBe(['id' => 'f', 'watermark' => null]);
    });

    it('sends true and false', function () {
        expect(FileDTO::make()->watermark(true)->toArray())->toBe(['watermark' => true])
            ->and(FolderDTO::make()->watermark(false)->toArray())->toBe(['watermark' => false]);
    });

    it('omits the field when it was not set', function () {
        expect(FileDTO::make()->id('a')->toArray())->toBe(['id' => 'a'])
            ->and(FolderDTO::make()->id('f')->toArray())->toBe(['id' => 'f']);
    });

    it('still drops other null values', function () {
        expect(FileDTO::make()->id('a')->alt(null)->watermark(null)->toArray())
            ->toBe(['id' => 'a', 'watermark' => null]);
    });
});
