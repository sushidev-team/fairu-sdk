<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

use DateTimeInterface;
use SushiDev\Fairu\Enums\LicenseType;

class LicenseDTO extends BaseDTO
{
    public function id(string $id): self
    {
        $this->data['id'] = $id;

        return $this;
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function copyrightId(?string $copyrightId): self
    {
        $this->data['copyright_id'] = $copyrightId;

        return $this;
    }

    public function interval(?int $interval): self
    {
        $this->data['interval'] = $interval;

        return $this;
    }

    public function default(bool $default): self
    {
        $this->data['default'] = $default;

        return $this;
    }

    public function active(bool $active): self
    {
        $this->data['active'] = $active;

        return $this;
    }

    public function start(DateTimeInterface|string|null $start): self
    {
        if ($start instanceof DateTimeInterface) {
            $this->data['start'] = $start->format('Y-m-d H:i:s');
        } else {
            $this->data['start'] = $start;
        }

        return $this;
    }

    public function end(DateTimeInterface|string|null $end): self
    {
        if ($end instanceof DateTimeInterface) {
            $this->data['end'] = $end->format('Y-m-d H:i:s');
        } else {
            $this->data['end'] = $end;
        }

        return $this;
    }

    public function replaceLicense(bool $replace): self
    {
        $this->data['replace_license'] = $replace;

        return $this;
    }

    public function replaceLicenseId(?string $licenseId): self
    {
        $this->data['replace_license_id'] = $licenseId;

        return $this;
    }

    public function replaceDate(DateTimeInterface|string|null $date): self
    {
        if ($date instanceof DateTimeInterface) {
            $this->data['replace_date'] = $date->format('Y-m-d H:i:s');
        } else {
            $this->data['replace_date'] = $date;
        }

        return $this;
    }

    public function type(LicenseType|string|null $type): self
    {
        $this->data['type'] = $type instanceof LicenseType ? $type->value : $type;

        return $this;
    }

    public function days(?int $days): self
    {
        $this->data['days'] = $days;

        return $this;
    }
}
