<?php

use SushiDev\Fairu\Enums\UploadType;
use SushiDev\Fairu\Enums\SortingDirection;
use SushiDev\Fairu\Enums\LicenseType;
use SushiDev\Fairu\Enums\WorkflowStatus;
use SushiDev\Fairu\Enums\WorkflowType;
use SushiDev\Fairu\Enums\UserStatus;
use SushiDev\Fairu\Enums\DiskType;
use SushiDev\Fairu\Enums\WebhookType;
use SushiDev\Fairu\Enums\CustomDomainStatus;
use SushiDev\Fairu\Enums\GallerySortingField;
use SushiDev\Fairu\Enums\VideoVersions;
use SushiDev\Fairu\Enums\DmcaStatus;
use SushiDev\Fairu\Enums\UploadShareLinkExpiration;
use SushiDev\Fairu\Enums\PdfSignatureRequestStatus;

describe('UploadType', function () {
    it('has correct values', function () {
        expect(UploadType::STANDARD->value)->toBe('STANDARD');
        expect(UploadType::DOWNLOAD->value)->toBe('DOWNLOAD');
    });

    it('can be created from string', function () {
        expect(UploadType::from('STANDARD'))->toBe(UploadType::STANDARD);
    });
});

describe('SortingDirection', function () {
    it('has ASC and DESC', function () {
        expect(SortingDirection::ASC->value)->toBe('ASC');
        expect(SortingDirection::DESC->value)->toBe('DESC');
    });
});

describe('LicenseType', function () {
    it('has correct values', function () {
        expect(LicenseType::STANDARD->value)->toBe('STANDARD');
        expect(LicenseType::PERIOD->value)->toBe('PERIOD');
    });
});

describe('WorkflowStatus', function () {
    it('has all statuses', function () {
        expect(WorkflowStatus::cases())->toHaveCount(5);
        expect(WorkflowStatus::NONE->value)->toBe('NONE');
        expect(WorkflowStatus::SUCCESS->value)->toBe('SUCCESS');
    });
});

describe('WorkflowType', function () {
    it('has copyright workflow types', function () {
        expect(WorkflowType::COPYRIGHT_SPLITTING->value)->toBe('COPYRIGHT_SPLITTING');
        expect(WorkflowType::COPYRIGHT_REPLACING->value)->toBe('COPYRIGHT_REPLACING');
    });
});

describe('UserStatus', function () {
    it('has correct user statuses', function () {
        expect(UserStatus::CREATED->value)->toBe('CREATED');
        expect(UserStatus::PENDING->value)->toBe('PENDING');
        expect(UserStatus::ACCEPTED->value)->toBe('ACCEPTED');
        expect(UserStatus::DECLINED->value)->toBe('DECLINED');
    });
});

describe('DiskType', function () {
    it('has storage types', function () {
        expect(DiskType::FTP->value)->toBe('FTP');
        expect(DiskType::SFTP->value)->toBe('SFTP');
        expect(DiskType::S3->value)->toBe('S3');
    });
});

describe('WebhookType', function () {
    it('has auth types', function () {
        expect(WebhookType::NONE->value)->toBe('NONE');
        expect(WebhookType::BASIC->value)->toBe('BASIC');
        expect(WebhookType::BEARER->value)->toBe('BEARER');
    });
});

describe('CustomDomainStatus', function () {
    it('has domain statuses', function () {
        expect(CustomDomainStatus::NONE->value)->toBe('NONE');
        expect(CustomDomainStatus::SUCCESS->value)->toBe('SUCCESS');
    });
});

describe('GallerySortingField', function () {
    it('has sorting fields', function () {
        expect(GallerySortingField::NAME->value)->toBe('NAME');
        expect(GallerySortingField::CREATED_AT->value)->toBe('CREATED_AT');
    });
});

describe('VideoVersions', function () {
    it('has quality levels', function () {
        expect(VideoVersions::LOW->value)->toBe('LOW');
        expect(VideoVersions::MEDIUM->value)->toBe('MEDIUM');
        expect(VideoVersions::HIGH->value)->toBe('HIGH');
    });
});

describe('DmcaStatus', function () {
    it('has DMCA statuses', function () {
        expect(DmcaStatus::OPEN->value)->toBe('OPEN');
        expect(DmcaStatus::ACCEPTED->value)->toBe('ACCEPTED');
    });
});

describe('UploadShareLinkExpiration', function () {
    it('has expiration options', function () {
        expect(UploadShareLinkExpiration::ONE_HOUR->value)->toBe('ONE_HOUR');
        expect(UploadShareLinkExpiration::NEVER->value)->toBe('NEVER');
    });
});

describe('PdfSignatureRequestStatus', function () {
    it('has signature statuses', function () {
        expect(PdfSignatureRequestStatus::CREATED->value)->toBe('CREATED');
        expect(PdfSignatureRequestStatus::DONE->value)->toBe('DONE');
    });
});
