<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class FolderFragments
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
        return FragmentBuilder::for('FairuFolder')
            ->name('FolderMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuFolder')
            ->name('FolderDefault')
            ->select([
                'id',
                'name',
                'auto_assign_copyright',
                'created_at',
                'updated_at',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuFolder')
            ->name('FolderFull')
            ->select([
                'id',
                'name',
                'auto_assign_copyright',
                'created_at',
                'updated_at',
            ])
            ->with('copyrights', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
                'email',
                'active',
            ]));
    }
}
