<?php

use SushiDev\Fairu\Fragments\FragmentBuilder;

describe('FragmentBuilder', function () {
    it('creates a basic fragment with selected fields', function () {
        $fragment = FragmentBuilder::for('FairuAsset')
            ->select(['id', 'name', 'url'])
            ->build();

        expect($fragment->getTypeName())->toBe('FairuAsset');
        expect($fragment->getFields())->toBe(['id', 'name', 'url']);
    });

    it('generates valid GraphQL selection', function () {
        $fragment = FragmentBuilder::for('FairuAsset')
            ->select(['id', 'name'])
            ->build();

        $graphql = $fragment->toGraphQL();

        expect($graphql)->toContain('id');
        expect($graphql)->toContain('name');
        expect($graphql)->toStartWith('{');
        expect($graphql)->toEndWith('}');
    });

    it('handles nested relations', function () {
        $fragment = FragmentBuilder::for('FairuAsset')
            ->select(['id', 'name'])
            ->with('copyrights', fn ($f) => $f->select(['id', 'name']))
            ->build();

        $graphql = $fragment->toGraphQL();

        expect($graphql)->toContain('copyrights');
    });

    it('can set a custom name', function () {
        $fragment = FragmentBuilder::for('FairuAsset')
            ->name('CustomFragment')
            ->select(['id'])
            ->build();

        expect($fragment->getName())->toBe('CustomFragment');
    });

    it('generates default name from type', function () {
        $fragment = FragmentBuilder::for('FairuAsset')
            ->select(['id'])
            ->build();

        expect($fragment->getName())->toBe('FairuAssetFragment');
    });

    it('can add single fields', function () {
        $fragment = FragmentBuilder::for('FairuAsset')
            ->field('id')
            ->field('name')
            ->build();

        expect($fragment->getFields())->toBe(['id', 'name']);
    });

    it('handles relation with array definition', function () {
        $fragment = FragmentBuilder::for('FairuAsset')
            ->select(['id'])
            ->with('copyrights', ['id', 'name', 'email'])
            ->build();

        $graphql = $fragment->toGraphQL();

        expect($graphql)->toContain('copyrights');
    });

    it('can be converted to string', function () {
        $fragment = FragmentBuilder::for('FairuAsset')
            ->select(['id'])
            ->build();

        expect((string) $fragment)->toContain('id');
    });
});
