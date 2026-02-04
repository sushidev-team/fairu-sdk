<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read array $entries
 * @property-read string|null $nextCursor
 * @property-read bool $hasMore
 */
class AllFilesFlatResult extends BaseResponse
{
    /**
     * @return FlatEntry[]
     */
    public function getEntries(): array
    {
        $entries = $this->data['entries'] ?? [];

        return array_map(fn ($entry) => new FlatEntry($entry), $entries);
    }

    public function getNextCursor(): ?string
    {
        return $this->data['nextCursor'] ?? null;
    }

    public function hasMore(): bool
    {
        return $this->data['hasMore'] ?? false;
    }
}
