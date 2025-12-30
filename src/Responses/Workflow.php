<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

use SushiDev\Fairu\Enums\WorkflowStatus;
use SushiDev\Fairu\Enums\WorkflowType;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string|null $type
 * @property-read bool|null $active
 * @property-read string|null $status
 * @property-read bool|null $has_error
 * @property-read string|null $last_at
 * @property-read string|null $created_at
 * @property-read string|null $updated_at
 */
class Workflow extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getType(): ?WorkflowType
    {
        $type = $this->data['type'] ?? null;

        return $type ? WorkflowType::tryFrom($type) : null;
    }

    public function getStatus(): ?WorkflowStatus
    {
        $status = $this->data['status'] ?? null;

        return $status ? WorkflowStatus::tryFrom($status) : null;
    }

    public function isActive(): bool
    {
        return $this->data['active'] ?? true;
    }

    public function hasError(): bool
    {
        return $this->data['has_error'] ?? false;
    }

    public function isProcessing(): bool
    {
        return $this->getStatus() === WorkflowStatus::PROCESSING;
    }
}
