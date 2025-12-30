<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

class PaginatorInfo extends BaseResponse
{
    public function getCurrentPage(): int
    {
        return $this->data['currentPage'] ?? 1;
    }

    public function getLastPage(): int
    {
        return $this->data['lastPage'] ?? 1;
    }

    public function getPerPage(): int
    {
        return $this->data['perPage'] ?? 20;
    }

    public function getTotal(): int
    {
        return $this->data['total'] ?? 0;
    }

    public function hasMorePages(): bool
    {
        return $this->data['hasMorePages'] ?? false;
    }

    public function isFirstPage(): bool
    {
        return $this->getCurrentPage() === 1;
    }

    public function isLastPage(): bool
    {
        return $this->getCurrentPage() === $this->getLastPage();
    }
}
