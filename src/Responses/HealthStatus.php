<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

class HealthStatus extends BaseResponse
{
    public function isHealthy(): bool
    {
        return $this->data['healthy'] ?? false;
    }
}
