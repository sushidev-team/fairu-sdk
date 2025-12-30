<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

class TenantCreationResult extends BaseResponse
{
    public function getTenant(): ?Tenant
    {
        if (isset($this->data['tenant'])) {
            return new Tenant($this->data['tenant']);
        }

        return null;
    }

    public function getApiKey(): ?string
    {
        return $this->data['api_key'] ?? null;
    }
}
