<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class TenantFragments
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
        return FragmentBuilder::for('FairuTenant')
            ->name('TenantMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuTenant')
            ->name('TenantDefault')
            ->select([
                'id',
                'name',
                'use_ai',
                'use_ai_onupload',
                'ai_language',
                'force_file_alt',
                'force_file_description',
                'force_file_caption',
                'force_filce_copyright',
                'force_file_policy',
                'force_license',
                'block_files_with_error',
                'custom_domain',
                'custom_domain_verified',
                'custom_domain_status',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuTenant')
            ->name('TenantFull')
            ->select([
                'id',
                'name',
                'use_ai',
                'use_ai_onupload',
                'ai_language',
                'force_file_alt',
                'force_file_description',
                'force_file_caption',
                'force_filce_copyright',
                'force_file_policy',
                'force_license',
                'block_files_with_error',
                'custom_domain',
                'custom_domain_verified',
                'custom_domain_status',
                'webhook_url',
                'webhook_type',
                'webhook_authorization',
                'trial_ends_at',
                'created_at',
                'updated_at',
            ])
            ->with('avatar', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
                'url',
            ]));
    }
}
