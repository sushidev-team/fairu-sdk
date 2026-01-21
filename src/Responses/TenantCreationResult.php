<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

class TenantCreationResult extends BaseResponse
{
    public function getId(): ?string
    {
        return $this->data['id'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getApiKey(): ?string
    {
        return $this->data['api_key'] ?? null;
    }

    /**
     * Get a Tenant object from this result.
     */
    public function getTenant(): ?Tenant
    {
        if (isset($this->data['id'])) {
            return new Tenant([
                'id' => $this->data['id'],
                'name' => $this->data['name'] ?? null,
            ]);
        }

        return null;
    }
}
