<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class UserFragments
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
        return FragmentBuilder::for('FairuUser')
            ->name('UserMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuUser')
            ->name('UserDefault')
            ->select([
                'id',
                'name',
                'email',
                'status',
                'owner',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuUser')
            ->name('UserFull')
            ->select([
                'id',
                'name',
                'email',
                'status',
                'owner',
            ]);
    }
}
