<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string $access_key_id
 * @property-read string $secret_access_key
 * @property-read string|null $bucket
 * @property-read array $permissions
 * @property-read bool $active
 * @property-read string $created_at
 */
class RakuCredentialWithSecret extends RakuCredential
{
    public function getSecretAccessKey(): string
    {
        return $this->data['secret_access_key'];
    }
}
