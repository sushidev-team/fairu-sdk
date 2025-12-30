<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

use SushiDev\Fairu\Enums\LicenseType;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string|null $type
 * @property-read bool|null $active
 * @property-read bool|null $default
 * @property-read string|null $start
 * @property-read string|null $end
 * @property-read int|null $interval
 * @property-read int|null $days
 * @property-read bool|null $replace_license
 * @property-read string|null $replace_date
 * @property-read string|null $created_at
 * @property-read string|null $updated_at
 */
class License extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getType(): ?LicenseType
    {
        $type = $this->data['type'] ?? null;

        return $type ? LicenseType::tryFrom($type) : null;
    }

    public function isActive(): bool
    {
        return $this->data['active'] ?? true;
    }

    public function isDefault(): bool
    {
        return $this->data['default'] ?? false;
    }

    public function getCopyright(): ?Copyright
    {
        if (isset($this->data['copyright'])) {
            return new Copyright($this->data['copyright']);
        }

        return null;
    }

    public function getReplaceLicenseEntry(): ?License
    {
        if (isset($this->data['replace_license_entry'])) {
            return new License($this->data['replace_license_entry']);
        }

        return null;
    }
}
