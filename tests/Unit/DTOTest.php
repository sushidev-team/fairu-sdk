<?php

use SushiDev\Fairu\DTOs\FileDTO;
use SushiDev\Fairu\DTOs\FolderDTO;
use SushiDev\Fairu\DTOs\GalleryDTO;
use SushiDev\Fairu\DTOs\CopyrightDTO;
use SushiDev\Fairu\DTOs\LicenseDTO;
use SushiDev\Fairu\DTOs\DiskDTO;
use SushiDev\Fairu\DTOs\DiskCredentialsDTO;
use SushiDev\Fairu\Enums\LicenseType;
use SushiDev\Fairu\Enums\DiskType;
use SushiDev\Fairu\Enums\GallerySortingField;

describe('FileDTO', function () {
    it('creates with fluent setters', function () {
        $dto = FileDTO::make()
            ->id('uuid-123')
            ->name('test.jpg')
            ->alt('Test image')
            ->description('A test image');

        $data = $dto->toArray();

        expect($data)->toHaveKey('id', 'uuid-123');
        expect($data)->toHaveKey('name', 'test.jpg');
        expect($data)->toHaveKey('alt', 'Test image');
    });

    it('filters out null values', function () {
        $dto = FileDTO::make()
            ->id('uuid-123')
            ->alt(null);

        $data = $dto->toArray();

        expect($data)->toHaveKey('id');
        expect($data)->not->toHaveKey('alt');
    });

    it('handles copyright and license IDs', function () {
        $dto = FileDTO::make()
            ->id('uuid-123')
            ->copyrightIds(['cr-1', 'cr-2'])
            ->licenseIds(['lic-1']);

        $data = $dto->toArray();

        expect($data['copyrightIds'])->toBe(['cr-1', 'cr-2']);
        expect($data['licenseIds'])->toBe(['lic-1']);
    });
});

describe('FolderDTO', function () {
    it('creates folder with parent', function () {
        $dto = FolderDTO::make()
            ->name('New Folder')
            ->parent('parent-uuid');

        $data = $dto->toArray();

        expect($data['name'])->toBe('New Folder');
        expect($data['parent'])->toBe('parent-uuid');
    });

    it('handles auto assign copyright', function () {
        $dto = FolderDTO::make()
            ->name('Folder')
            ->autoAssignCopyright(true)
            ->copyrightIds(['cr-1']);

        $data = $dto->toArray();

        expect($data['autoAssignCopyright'])->toBeTrue();
    });
});

describe('GalleryDTO', function () {
    it('handles date as DateTime', function () {
        $date = new DateTime('2024-01-15 10:00:00');

        $dto = GalleryDTO::make()
            ->name('Event')
            ->date($date);

        $data = $dto->toArray();

        expect($data['date'])->toBe('2024-01-15 10:00:00');
    });

    it('handles sorting field enum', function () {
        $dto = GalleryDTO::make()
            ->name('Gallery')
            ->sortingField(GallerySortingField::CREATED_AT);

        $data = $dto->toArray();

        expect($data['sorting_field'])->toBe('CREATED_AT');
    });
});

describe('CopyrightDTO', function () {
    it('creates copyright with contact info', function () {
        $dto = CopyrightDTO::make()
            ->name('John Doe')
            ->email('john@example.com')
            ->phone('+1234567890')
            ->website('https://example.com');

        $data = $dto->toArray();

        expect($data['name'])->toBe('John Doe');
        expect($data['email'])->toBe('john@example.com');
    });
});

describe('LicenseDTO', function () {
    it('handles license type enum', function () {
        $dto = LicenseDTO::make()
            ->name('Standard License')
            ->type(LicenseType::STANDARD);

        $data = $dto->toArray();

        expect($data['type'])->toBe('STANDARD');
    });

    it('handles period license with dates', function () {
        $dto = LicenseDTO::make()
            ->name('Yearly License')
            ->type(LicenseType::PERIOD)
            ->start('2024-01-01 00:00:00')
            ->end('2024-12-31 23:59:59');

        $data = $dto->toArray();

        expect($data['type'])->toBe('PERIOD');
        expect($data['start'])->toBe('2024-01-01 00:00:00');
    });
});

describe('DiskDTO', function () {
    it('handles disk type enum', function () {
        $dto = DiskDTO::make()
            ->name('S3 Backup')
            ->type(DiskType::S3);

        $data = $dto->toArray();

        expect($data['type'])->toBe('S3');
    });

    it('handles nested credentials', function () {
        $credentials = DiskCredentialsDTO::make()
            ->key('access-key')
            ->secret('secret-key')
            ->bucket('my-bucket')
            ->region('eu-west-1');

        $dto = DiskDTO::make()
            ->name('S3 Disk')
            ->type(DiskType::S3)
            ->credentials($credentials);

        $data = $dto->toArray();

        expect($data['credentials'])->toBeArray();
        expect($data['credentials']['bucket'])->toBe('my-bucket');
    });
});
