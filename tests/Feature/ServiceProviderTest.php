<?php

use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Cache\CacheManager;
use SushiDev\Fairu\Fragments\FragmentRegistry;
use SushiDev\Fairu\Facades\Fairu;

describe('ServiceProvider', function () {
    it('registers FairuClient as singleton', function () {
        $client1 = app(FairuClient::class);
        $client2 = app(FairuClient::class);

        expect($client1)->toBeInstanceOf(FairuClient::class);
        expect($client1)->toBe($client2);
    });

    it('registers FragmentRegistry as singleton', function () {
        $registry1 = app(FragmentRegistry::class);
        $registry2 = app(FragmentRegistry::class);

        expect($registry1)->toBeInstanceOf(FragmentRegistry::class);
        expect($registry1)->toBe($registry2);
    });

    it('registers CacheManager as singleton', function () {
        $cache1 = app(CacheManager::class);
        $cache2 = app(CacheManager::class);

        expect($cache1)->toBeInstanceOf(CacheManager::class);
        expect($cache1)->toBe($cache2);
    });

    it('registers fairu alias', function () {
        $client = app('fairu');

        expect($client)->toBeInstanceOf(FairuClient::class);
    });

    it('uses config values', function () {
        $client = app(FairuClient::class);

        expect($client->getBaseUrl())->toBe('https://fairu.test');
    });
});

describe('Facade', function () {
    it('resolves to FairuClient', function () {
        expect(Fairu::getFacadeRoot())->toBeInstanceOf(FairuClient::class);
    });

    it('provides query accessors', function () {
        expect(Fairu::health())->toBeInstanceOf(\SushiDev\Fairu\Queries\HealthQueries::class);
        expect(Fairu::assets())->toBeInstanceOf(\SushiDev\Fairu\Queries\AssetQueries::class);
        expect(Fairu::folders())->toBeInstanceOf(\SushiDev\Fairu\Queries\FolderQueries::class);
        expect(Fairu::galleries())->toBeInstanceOf(\SushiDev\Fairu\Queries\GalleryQueries::class);
        expect(Fairu::copyrights())->toBeInstanceOf(\SushiDev\Fairu\Queries\CopyrightQueries::class);
        expect(Fairu::licenses())->toBeInstanceOf(\SushiDev\Fairu\Queries\LicenseQueries::class);
        expect(Fairu::workflows())->toBeInstanceOf(\SushiDev\Fairu\Queries\WorkflowQueries::class);
        expect(Fairu::users())->toBeInstanceOf(\SushiDev\Fairu\Queries\UserQueries::class);
        expect(Fairu::roles())->toBeInstanceOf(\SushiDev\Fairu\Queries\RoleQueries::class);
        expect(Fairu::disks())->toBeInstanceOf(\SushiDev\Fairu\Queries\DiskQueries::class);
        expect(Fairu::dmcas())->toBeInstanceOf(\SushiDev\Fairu\Queries\DmcaQueries::class);
        expect(Fairu::tenant())->toBeInstanceOf(\SushiDev\Fairu\Queries\TenantQueries::class);
    });

    it('provides mutation accessors', function () {
        expect(Fairu::uploads())->toBeInstanceOf(\SushiDev\Fairu\Mutations\UploadMutations::class);
        expect(Fairu::assetMutations())->toBeInstanceOf(\SushiDev\Fairu\Mutations\AssetMutations::class);
        expect(Fairu::folderMutations())->toBeInstanceOf(\SushiDev\Fairu\Mutations\FolderMutations::class);
    });

    it('provides fragment registry', function () {
        expect(Fairu::fragments())->toBeInstanceOf(FragmentRegistry::class);
    });
});
