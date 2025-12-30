<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class LicenseFragments
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
        return FragmentBuilder::for('FairuLicense')
            ->name('LicenseMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuLicense')
            ->name('LicenseDefault')
            ->select([
                'id',
                'name',
                'type',
                'active',
                'default',
                'start',
                'end',
                'interval',
                'days',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuLicense')
            ->name('LicenseFull')
            ->select([
                'id',
                'name',
                'type',
                'active',
                'default',
                'start',
                'end',
                'interval',
                'days',
                'replace_license',
                'replace_date',
                'created_at',
                'updated_at',
            ])
            ->with('copyright', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
                'email',
            ]))
            ->with('replace_license_entry', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
            ]))
            ->with('replaced_by_license_entry', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
            ]));
    }
}
