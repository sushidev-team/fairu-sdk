<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Fragments;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\AssetFragments;
use SushiDev\Fairu\Fragments\Predefined\CopyrightFragments;
use SushiDev\Fairu\Fragments\Predefined\DiskFragments;
use SushiDev\Fairu\Fragments\Predefined\DmcaFragments;
use SushiDev\Fairu\Fragments\Predefined\FolderFragments;
use SushiDev\Fairu\Fragments\Predefined\GalleryFragments;
use SushiDev\Fairu\Fragments\Predefined\LicenseFragments;
use SushiDev\Fairu\Fragments\Predefined\RoleFragments;
use SushiDev\Fairu\Fragments\Predefined\TenantFragments;
use SushiDev\Fairu\Fragments\Predefined\UserFragments;
use SushiDev\Fairu\Fragments\Predefined\WorkflowFragments;

class FragmentRegistry
{
    private array $customFragments = [];

    public function asset(string $variant = 'default'): FragmentInterface
    {
        return AssetFragments::get($variant);
    }

    public function folder(string $variant = 'default'): FragmentInterface
    {
        return FolderFragments::get($variant);
    }

    public function gallery(string $variant = 'default'): FragmentInterface
    {
        return GalleryFragments::get($variant);
    }

    public function copyright(string $variant = 'default'): FragmentInterface
    {
        return CopyrightFragments::get($variant);
    }

    public function license(string $variant = 'default'): FragmentInterface
    {
        return LicenseFragments::get($variant);
    }

    public function workflow(string $variant = 'default'): FragmentInterface
    {
        return WorkflowFragments::get($variant);
    }

    public function user(string $variant = 'default'): FragmentInterface
    {
        return UserFragments::get($variant);
    }

    public function role(string $variant = 'default'): FragmentInterface
    {
        return RoleFragments::get($variant);
    }

    public function disk(string $variant = 'default'): FragmentInterface
    {
        return DiskFragments::get($variant);
    }

    public function tenant(string $variant = 'default'): FragmentInterface
    {
        return TenantFragments::get($variant);
    }

    public function dmca(string $variant = 'default'): FragmentInterface
    {
        return DmcaFragments::get($variant);
    }

    public function register(string $name, FragmentInterface $fragment): void
    {
        $this->customFragments[$name] = $fragment;
    }

    public function get(string $name): ?FragmentInterface
    {
        return $this->customFragments[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return isset($this->customFragments[$name]);
    }

    public function builder(string $typeName): FragmentBuilder
    {
        return FragmentBuilder::for($typeName);
    }
}
