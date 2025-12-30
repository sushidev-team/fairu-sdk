<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

use SushiDev\Fairu\Enums\CustomDomainStatus;
use SushiDev\Fairu\Enums\WebhookType;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read bool|null $use_ai
 * @property-read bool|null $use_ai_onupload
 * @property-read string|null $ai_language
 * @property-read bool|null $force_file_alt
 * @property-read bool|null $force_file_description
 * @property-read bool|null $force_file_caption
 * @property-read bool|null $force_filce_copyright
 * @property-read bool|null $force_file_policy
 * @property-read bool|null $force_license
 * @property-read bool|null $block_files_with_error
 * @property-read string|null $custom_domain
 * @property-read bool|null $custom_domain_verified
 * @property-read string|null $custom_domain_status
 * @property-read string|null $webhook_url
 * @property-read string|null $webhook_type
 * @property-read string|null $trial_ends_at
 * @property-read string|null $created_at
 * @property-read string|null $updated_at
 */
class Tenant extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function usesAi(): bool
    {
        return $this->data['use_ai'] ?? false;
    }

    public function usesAiOnUpload(): bool
    {
        return $this->data['use_ai_onupload'] ?? false;
    }

    public function getCustomDomain(): ?string
    {
        return $this->data['custom_domain'] ?? null;
    }

    public function getCustomDomainStatus(): ?CustomDomainStatus
    {
        $status = $this->data['custom_domain_status'] ?? null;

        return $status ? CustomDomainStatus::tryFrom($status) : null;
    }

    public function isCustomDomainVerified(): bool
    {
        return $this->data['custom_domain_verified'] ?? false;
    }

    public function getWebhookType(): ?WebhookType
    {
        $type = $this->data['webhook_type'] ?? null;

        return $type ? WebhookType::tryFrom($type) : null;
    }

    public function getAvatar(): ?Asset
    {
        if (isset($this->data['avatar'])) {
            return new Asset($this->data['avatar']);
        }

        return null;
    }
}
