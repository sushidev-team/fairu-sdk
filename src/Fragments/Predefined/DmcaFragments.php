<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class DmcaFragments
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
        return FragmentBuilder::for('FairuDmca')
            ->name('DmcaMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuDmca')
            ->name('DmcaDefault')
            ->select([
                'id',
                'name',
                'email',
                'status',
                'reply_send',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuDmca')
            ->name('DmcaFull')
            ->select([
                'id',
                'name',
                'email',
                'reply',
                'reply_send',
                'status',
            ])
            ->with('file', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
                'url',
            ]));
    }
}
