<?php

use SushiDev\Fairu\Responses\Asset;
use SushiDev\Fairu\Responses\Folder;
use SushiDev\Fairu\Responses\Gallery;
use SushiDev\Fairu\Responses\Copyright;
use SushiDev\Fairu\Responses\License;
use SushiDev\Fairu\Responses\User;
use SushiDev\Fairu\Responses\Role;
use SushiDev\Fairu\Responses\Tenant;
use SushiDev\Fairu\Responses\PaginatedList;
use SushiDev\Fairu\Responses\PaginatorInfo;
use SushiDev\Fairu\Enums\LicenseType;
use SushiDev\Fairu\Enums\UserStatus;

describe('Asset', function () {
    it('creates from array data', function () {
        $asset = new Asset([
            'id' => 'uuid-123',
            'name' => 'test.jpg',
            'mime' => 'image/jpeg',
            'url' => 'https://example.com/test.jpg',
            'width' => 1920,
            'height' => 1080,
        ]);

        expect($asset->getId())->toBe('uuid-123');
        expect($asset->getName())->toBe('test.jpg');
        expect($asset->getMime())->toBe('image/jpeg');
        expect($asset->getUrl())->toBe('https://example.com/test.jpg');
    });

    it('detects image type', function () {
        $image = new Asset(['id' => '1', 'mime' => 'image/jpeg']);
        $video = new Asset(['id' => '2', 'mime' => 'video/mp4']);
        $pdf = new Asset(['id' => '3', 'mime' => 'application/pdf']);

        expect($image->isImage())->toBeTrue();
        expect($video->isImage())->toBeFalse();
        expect($video->isVideo())->toBeTrue();
        expect($pdf->isPdf())->toBeTrue();
    });

    it('calculates aspect ratio', function () {
        $asset = new Asset([
            'id' => '1',
            'width' => 1920,
            'height' => 1080,
        ]);

        expect($asset->getAspectRatio())->toBeFloat();
        expect($asset->getAspectRatio())->toBeGreaterThan(1.7);
    });

    it('handles nested copyrights', function () {
        $asset = new Asset([
            'id' => '1',
            'copyrights' => [
                ['id' => 'cr-1', 'name' => 'Copyright 1'],
                ['id' => 'cr-2', 'name' => 'Copyright 2'],
            ],
        ]);

        $copyrights = $asset->getCopyrights();

        expect($copyrights)->toHaveCount(2);
        expect($copyrights[0])->toBeInstanceOf(Copyright::class);
    });

    it('supports array access', function () {
        $asset = new Asset(['id' => '1', 'name' => 'test.jpg']);

        expect($asset['id'])->toBe('1');
        expect($asset['name'])->toBe('test.jpg');
        expect(isset($asset['id']))->toBeTrue();
    });

    it('supports property access', function () {
        $asset = new Asset(['id' => '1', 'name' => 'test.jpg']);

        expect($asset->id)->toBe('1');
        expect($asset->name)->toBe('test.jpg');
    });

    it('serializes to JSON', function () {
        $asset = new Asset(['id' => '1', 'name' => 'test.jpg']);

        $json = json_encode($asset);
        $decoded = json_decode($json, true);

        expect($decoded['id'])->toBe('1');
    });
});

describe('Folder', function () {
    it('creates from array data', function () {
        $folder = new Folder([
            'id' => 'folder-1',
            'name' => 'My Folder',
            'auto_assign_copyright' => true,
        ]);

        expect($folder->getId())->toBe('folder-1');
        expect($folder->getName())->toBe('My Folder');
        expect($folder->hasAutoAssignCopyright())->toBeTrue();
    });
});

describe('Gallery', function () {
    it('creates with cover image', function () {
        $gallery = new Gallery([
            'id' => 'gallery-1',
            'name' => 'Event 2024',
            'cover_image' => [
                'id' => 'cover-1',
                'url' => 'https://example.com/cover.jpg',
            ],
        ]);

        expect($gallery->getName())->toBe('Event 2024');
        expect($gallery->getCoverImage())->toBeInstanceOf(Asset::class);
        expect($gallery->getCoverImage()->getUrl())->toBe('https://example.com/cover.jpg');
    });

    it('returns null for missing cover image', function () {
        $gallery = new Gallery(['id' => '1', 'name' => 'No Cover']);

        expect($gallery->getCoverImage())->toBeNull();
    });
});

describe('License', function () {
    it('parses license type', function () {
        $license = new License([
            'id' => 'lic-1',
            'name' => 'Standard',
            'type' => 'STANDARD',
        ]);

        expect($license->getType())->toBe(LicenseType::STANDARD);
    });

    it('handles nested copyright', function () {
        $license = new License([
            'id' => 'lic-1',
            'copyright' => [
                'id' => 'cr-1',
                'name' => 'Owner',
            ],
        ]);

        expect($license->getCopyright())->toBeInstanceOf(Copyright::class);
    });
});

describe('User', function () {
    it('parses user status', function () {
        $user = new User([
            'id' => 'user-1',
            'name' => 'John',
            'email' => 'john@example.com',
            'status' => 'ACCEPTED',
            'owner' => false,
        ]);

        expect($user->getStatus())->toBe(UserStatus::ACCEPTED);
        expect($user->isAccepted())->toBeTrue();
        expect($user->isPending())->toBeFalse();
        expect($user->isOwner())->toBeFalse();
    });
});

describe('Role', function () {
    it('checks permissions', function () {
        $role = new Role([
            'id' => 'role-1',
            'name' => 'Editor',
            'permissions' => ['read', 'write', 'delete'],
        ]);

        expect($role->hasPermission('read'))->toBeTrue();
        expect($role->hasPermission('admin'))->toBeFalse();
    });
});

describe('PaginatedList', function () {
    it('creates from paginated response', function () {
        $list = new PaginatedList([
            'data' => [
                ['id' => '1', 'name' => 'Asset 1'],
                ['id' => '2', 'name' => 'Asset 2'],
            ],
            'paginatorInfo' => [
                'currentPage' => 1,
                'lastPage' => 5,
                'perPage' => 20,
                'total' => 100,
                'hasMorePages' => true,
            ],
        ], fn ($item) => new Asset($item));

        expect($list->count())->toBe(2);
        expect($list->total())->toBe(100);
        expect($list->currentPage())->toBe(1);
        expect($list->hasMorePages())->toBeTrue();
    });

    it('supports first and last', function () {
        $list = new PaginatedList([
            'data' => [
                ['id' => '1'],
                ['id' => '2'],
            ],
            'paginatorInfo' => [],
        ], fn ($item) => new Asset($item));

        expect($list->first()->getId())->toBe('1');
        expect($list->last()->getId())->toBe('2');
    });

    it('supports iteration', function () {
        $list = new PaginatedList([
            'data' => [
                ['id' => '1'],
                ['id' => '2'],
            ],
            'paginatorInfo' => [],
        ], fn ($item) => new Asset($item));

        $ids = [];
        foreach ($list as $item) {
            $ids[] = $item->getId();
        }

        expect($ids)->toBe(['1', '2']);
    });

    it('supports map and pluck', function () {
        $list = new PaginatedList([
            'data' => [
                ['id' => '1', 'name' => 'First'],
                ['id' => '2', 'name' => 'Second'],
            ],
            'paginatorInfo' => [],
        ], fn ($item) => new Asset($item));

        $ids = $list->pluck('id');
        expect($ids)->toBe(['1', '2']);
    });
});

describe('PaginatorInfo', function () {
    it('provides pagination helpers', function () {
        $info = new PaginatorInfo([
            'currentPage' => 1,
            'lastPage' => 5,
            'perPage' => 20,
            'total' => 100,
            'hasMorePages' => true,
        ]);

        expect($info->isFirstPage())->toBeTrue();
        expect($info->isLastPage())->toBeFalse();
        expect($info->getTotal())->toBe(100);
    });
});
