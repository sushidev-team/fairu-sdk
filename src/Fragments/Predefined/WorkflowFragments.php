<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class WorkflowFragments
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
        return FragmentBuilder::for('FairuWorkflow')
            ->name('WorkflowMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuWorkflow')
            ->name('WorkflowDefault')
            ->select([
                'id',
                'name',
                'type',
                'active',
                'status',
                'has_error',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuWorkflow')
            ->name('WorkflowFull')
            ->select([
                'id',
                'name',
                'type',
                'active',
                'status',
                'has_error',
                'last_at',
                'created_at',
                'updated_at',
            ]);
    }
}
