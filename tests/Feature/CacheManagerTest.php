<?php

use SushiDev\Fairu\Cache\CacheManager;
use Illuminate\Support\Facades\Cache;

describe('CacheManager', function () {
    beforeEach(function () {
        Cache::flush();
    });

    it('stores and retrieves values', function () {
        config(['fairu.cache.enabled' => true]);

        $manager = new CacheManager(app('cache'), config('fairu.cache'));

        $manager->put('test_key', ['data' => 'value']);

        expect($manager->get('test_key'))->toBe(['data' => 'value']);
    });

    it('respects enabled setting', function () {
        $manager = new CacheManager(app('cache'), [
            'enabled' => false,
            'prefix' => 'fairu_',
        ]);

        $manager->put('test_key', 'value');

        expect($manager->get('test_key'))->toBeNull();
    });

    it('generates cache keys', function () {
        $manager = new CacheManager(app('cache'), ['enabled' => true, 'prefix' => 'fairu_']);

        $key1 = $manager->generateKey('query', ['id' => '123']);
        $key2 = $manager->generateKey('query', ['id' => '123']);
        $key3 = $manager->generateKey('query', ['id' => '456']);

        expect($key1)->toBe($key2);
        expect($key1)->not->toBe($key3);
    });

    it('uses remember pattern', function () {
        config(['fairu.cache.enabled' => true]);

        $manager = new CacheManager(app('cache'), config('fairu.cache'));
        $callCount = 0;

        $result1 = $manager->remember('remember_key', function () use (&$callCount) {
            $callCount++;
            return 'computed_value';
        });

        $result2 = $manager->remember('remember_key', function () use (&$callCount) {
            $callCount++;
            return 'new_value';
        });

        expect($result1)->toBe('computed_value');
        expect($result2)->toBe('computed_value');
        expect($callCount)->toBe(1);
    });

    it('forgets cached values', function () {
        config(['fairu.cache.enabled' => true]);

        $manager = new CacheManager(app('cache'), config('fairu.cache'));

        $manager->put('forget_key', 'value');
        expect($manager->get('forget_key'))->toBe('value');

        $manager->forget('forget_key');
        expect($manager->get('forget_key'))->toBeNull();
    });

    it('returns correct TTL for resource types', function () {
        $manager = new CacheManager(app('cache'), [
            'enabled' => true,
            'ttl' => [
                'tenant' => 3600,
                'assets' => 300,
                'default' => 600,
            ],
        ]);

        expect($manager->getTtl('tenant'))->toBe(3600);
        expect($manager->getTtl('assets'))->toBe(300);
        expect($manager->getTtl('unknown'))->toBe(600);
        expect($manager->getTtl())->toBe(600);
    });

    it('checks if enabled', function () {
        $enabled = new CacheManager(app('cache'), ['enabled' => true]);
        $disabled = new CacheManager(app('cache'), ['enabled' => false]);

        expect($enabled->isEnabled())->toBeTrue();
        expect($disabled->isEnabled())->toBeFalse();
    });
});
