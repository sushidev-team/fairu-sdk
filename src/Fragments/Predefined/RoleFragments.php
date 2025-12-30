<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class RoleFragments
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
        return FragmentBuilder::for('FairuRole')
            ->name('RoleMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuRole')
            ->name('RoleDefault')
            ->select([
                'id',
                'name',
                'permissions',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuRole')
            ->name('RoleFull')
            ->select([
                'id',
                'name',
                'permissions',
                'created_at',
                'updated_at',
            ]);
    }
}
