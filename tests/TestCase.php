<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SushiDev\Fairu\FairuServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            FairuServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Fairu' => \SushiDev\Fairu\Facades\Fairu::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('fairu.base_url', 'https://fairu.test');
        $app['config']->set('fairu.token', 'test-token');
        $app['config']->set('fairu.timeout', 30);
        $app['config']->set('fairu.cache.enabled', false);
    }
}
