<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read string $id
 * @property-read bool|null $syncing
 * @property-read int|null $open
 * @property-read int|null $pending
 * @property-read int|null $synced
 * @property-read int|null $failed
 */
class DiskStatus extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function isSyncing(): bool
    {
        return $this->data['syncing'] ?? false;
    }

    public function getOpen(): int
    {
        return $this->data['open'] ?? 0;
    }

    public function getPending(): int
    {
        return $this->data['pending'] ?? 0;
    }

    public function getSynced(): int
    {
        return $this->data['synced'] ?? 0;
    }

    public function getFailed(): int
    {
        return $this->data['failed'] ?? 0;
    }

    public function getTotal(): int
    {
        return $this->getOpen() + $this->getPending() + $this->getSynced() + $this->getFailed();
    }

    public function getProgress(): float
    {
        $total = $this->getTotal();

        if ($total === 0) {
            return 100.0;
        }

        return ($this->getSynced() / $total) * 100;
    }
}
