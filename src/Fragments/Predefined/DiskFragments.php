<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class DiskFragments
{
    public static function get(string $variant = 'default'): FragmentInterface
    {
        return match ($variant) {
            'minimal' => self::minimal(),
            'full' => self::full(),
            default => self::default(),
        };
    }

    public static function minimal(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuDisk')
            ->name('DiskMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuDisk')
            ->name('DiskDefault')
            ->select([
                'id',
                'name',
                'type',
                'path',
                'pattern',
                'active',
                'healthy',
                'syncing',
                'delete_at_origin',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuDisk')
            ->name('DiskFull')
            ->select([
                'id',
                'name',
                'type',
                'path',
                'pattern',
                'active',
                'healthy',
                'syncing',
                'delete_at_origin',
                'created_at',
                'updated_at',
            ])
            ->with('folder', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
            ]))
            ->with('credentials', fn (FragmentBuilder $f) => $f->select([
                'ftp_host',
                'ftp_port',
                'ftp_username',
                'key',
                'region',
                'bucket',
                'endpoint',
                'url',
            ]));
    }
}
