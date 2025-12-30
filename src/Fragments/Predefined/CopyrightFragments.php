<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class CopyrightFragments
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
        return FragmentBuilder::for('FairuCopyright')
            ->name('CopyrightMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuCopyright')
            ->name('CopyrightDefault')
            ->select([
                'id',
                'name',
                'email',
                'active',
                'blocked',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuCopyright')
            ->name('CopyrightFull')
            ->select([
                'id',
                'name',
                'email',
                'phone',
                'website',
                'active',
                'blocked',
                'created_at',
                'updated_at',
            ]);
    }
}
