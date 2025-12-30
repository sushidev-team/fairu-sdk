<?php

declare(strict_types=1);

namespace SushiDev\Fairu;

use Illuminate\Support\ServiceProvider;
use SushiDev\Fairu\Cache\CacheManager;
use SushiDev\Fairu\Fragments\FragmentRegistry;

class FairuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/fairu.php', 'fairu');

        $this->app->singleton(FragmentRegistry::class, function ($app) {
            return new FragmentRegistry();
        });

        $this->app->singleton(CacheManager::class, function ($app) {
            return new CacheManager(
                $app['cache'],
                $app['config']->get('fairu.cache')
            );
        });

        $this->app->singleton(FairuClient::class, function ($app) {
            return new FairuClient(
                baseUrl: $app['config']->get('fairu.base_url'),
                token: $app['config']->get('fairu.token'),
                timeout: $app['config']->get('fairu.timeout', 30),
                retryConfig: $app['config']->get('fairu.retry', []),
                cacheManager: $app->make(CacheManager::class),
                fragmentRegistry: $app->make(FragmentRegistry::class),
                fileProxyUrl: $app['config']->get('fairu.file_proxy_url'),
            );
        });

        $this->app->alias(FairuClient::class, 'fairu');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/fairu.php' => config_path('fairu.php'),
            ], 'fairu-config');
        }
    }

    public function provides(): array
    {
        return [
            FairuClient::class,
            FragmentRegistry::class,
            CacheManager::class,
            'fairu',
        ];
    }
}
