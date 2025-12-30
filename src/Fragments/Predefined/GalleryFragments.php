<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class GalleryFragments
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
        return FragmentBuilder::for('FairuGallery')
            ->name('GalleryMinimal')
            ->select(['id', 'name']);
    }

    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuGallery')
            ->name('GalleryDefault')
            ->select([
                'id',
                'name',
                'description',
                'date',
                'location',
                'active',
                'exclude_from_list',
                'sorting_field',
                'sorting_direction',
                'copyright_text',
            ])
            ->with('cover_image', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
                'url',
                'blurhash',
            ]));
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuGallery')
            ->name('GalleryFull')
            ->select([
                'id',
                'name',
                'description',
                'date',
                'location',
                'active',
                'exclude_from_list',
                'sorting_field',
                'sorting_direction',
                'copyright_text',
            ])
            ->with('cover_image', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
                'url',
                'width',
                'height',
                'blurhash',
                'alt',
            ]))
            ->with('items', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
                'url',
                'width',
                'height',
                'blurhash',
                'alt',
                'mime',
            ]))
            ->with('copyrights', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
            ]));
    }
}
