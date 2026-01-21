<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments\Predefined;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;

class AssetFragments
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
        return FragmentBuilder::for('FairuAsset')
            ->name('AssetMinimal')
            ->select(['id', 'name']);
    }

    /**
     * Default asset fragment.
     *
     * Note: created_at/updated_at omitted to avoid type conflicts in union queries
     * until the API is updated to use DateTime instead of String.
     */
    public static function default(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuAsset')
            ->name('AssetDefault')
            ->select([
                'id',
                'name',
                'mime',
                'alt',
                'caption',
                'description',
                'url',
                'width',
                'height',
                'blurhash',
                'focal_point',
                'blocked',
                'has_error',
            ]);
    }

    public static function full(): FragmentBuilder
    {
        return FragmentBuilder::for('FairuAsset')
            ->name('AssetFull')
            ->select([
                'id',
                'name',
                'mime',
                'alt',
                'caption',
                'description',
                'copyright_text',
                'url',
                'width',
                'height',
                'original_width',
                'original_height',
                'blurhash',
                'focal_point',
                'blocked',
                'has_error',
                'size',
                'versions',
                'created_at',
                'updated_at',
            ])
            ->with('copyrights', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
                'email',
                'phone',
                'website',
                'active',
            ]))
            ->with('licenses', fn (FragmentBuilder $f) => $f->select([
                'id',
                'name',
                'type',
                'start',
                'end',
                'active',
            ]));
    }

    public static function withUrls(int $width = null, int $height = null, int $quality = null): FragmentBuilder
    {
        $builder = FragmentBuilder::for('FairuAsset')
            ->name('AssetWithUrls')
            ->select([
                'id',
                'name',
                'mime',
                'alt',
                'width',
                'height',
                'blurhash',
                'focal_point',
            ]);

        $urlArgs = array_filter([
            'width' => $width,
            'height' => $height,
            'quality' => $quality,
            'withStoredFocalPoint' => true,
        ], fn ($v) => $v !== null);

        if (! empty($urlArgs)) {
            $builder->withArguments('url', $urlArgs, []);
        } else {
            $builder->field('url');
        }

        return $builder;
    }
}
