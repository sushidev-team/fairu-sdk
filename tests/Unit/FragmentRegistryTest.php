<?php

use SushiDev\Fairu\Fragments\FragmentBuilder;
use SushiDev\Fairu\Fragments\FragmentRegistry;
use SushiDev\Fairu\Contracts\FragmentInterface;

describe('FragmentRegistry', function () {
    it('returns predefined asset fragments', function () {
        $registry = new FragmentRegistry();

        $minimal = $registry->asset('minimal');
        $default = $registry->asset('default');
        $full = $registry->asset('full');

        expect($minimal)->toBeInstanceOf(FragmentInterface::class);
        expect($default)->toBeInstanceOf(FragmentInterface::class);
        expect($full)->toBeInstanceOf(FragmentInterface::class);
    });

    it('returns predefined folder fragments', function () {
        $registry = new FragmentRegistry();

        $minimal = $registry->folder('minimal');
        $default = $registry->folder('default');

        expect($minimal)->toBeInstanceOf(FragmentInterface::class);
        expect($default)->toBeInstanceOf(FragmentInterface::class);
    });

    it('returns predefined gallery fragments', function () {
        $registry = new FragmentRegistry();

        $default = $registry->gallery('default');

        expect($default)->toBeInstanceOf(FragmentInterface::class);
    });

    it('returns predefined copyright fragments', function () {
        $registry = new FragmentRegistry();

        $default = $registry->copyright('default');

        expect($default)->toBeInstanceOf(FragmentInterface::class);
    });

    it('returns predefined license fragments', function () {
        $registry = new FragmentRegistry();

        $default = $registry->license('default');

        expect($default)->toBeInstanceOf(FragmentInterface::class);
    });

    it('returns predefined user fragments', function () {
        $registry = new FragmentRegistry();

        $default = $registry->user('default');

        expect($default)->toBeInstanceOf(FragmentInterface::class);
    });

    it('returns predefined role fragments', function () {
        $registry = new FragmentRegistry();

        $default = $registry->role('default');

        expect($default)->toBeInstanceOf(FragmentInterface::class);
    });

    it('returns predefined tenant fragments', function () {
        $registry = new FragmentRegistry();

        $default = $registry->tenant('default');

        expect($default)->toBeInstanceOf(FragmentInterface::class);
    });

    it('can register custom fragments', function () {
        $registry = new FragmentRegistry();

        $custom = FragmentBuilder::for('Custom')
            ->select(['id', 'custom_field'])
            ->build();

        $registry->register('my_custom', $custom);

        expect($registry->has('my_custom'))->toBeTrue();
        expect($registry->get('my_custom'))->toBe($custom);
    });

    it('returns null for unregistered fragments', function () {
        $registry = new FragmentRegistry();

        expect($registry->get('nonexistent'))->toBeNull();
        expect($registry->has('nonexistent'))->toBeFalse();
    });

    it('provides a builder shortcut', function () {
        $registry = new FragmentRegistry();

        $builder = $registry->builder('FairuAsset');

        expect($builder)->toBeInstanceOf(FragmentBuilder::class);
        expect($builder->getTypeName())->toBe('FairuAsset');
    });
});
