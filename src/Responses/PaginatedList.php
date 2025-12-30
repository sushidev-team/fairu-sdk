<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class PaginatedList implements Countable, IteratorAggregate
{
    private array $items;

    private PaginatorInfo $paginatorInfo;

    public function __construct(array $data, ?callable $itemTransformer = null)
    {
        $items = $data['data'] ?? [];

        if ($itemTransformer) {
            $this->items = array_map($itemTransformer, $items);
        } else {
            $this->items = $items;
        }

        $this->paginatorInfo = new PaginatorInfo($data['paginatorInfo'] ?? []);
    }

    public function items(): array
    {
        return $this->items;
    }

    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    public function last(): mixed
    {
        return $this->items[array_key_last($this->items)] ?? null;
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function total(): int
    {
        return $this->paginatorInfo->getTotal();
    }

    public function currentPage(): int
    {
        return $this->paginatorInfo->getCurrentPage();
    }

    public function lastPage(): int
    {
        return $this->paginatorInfo->getLastPage();
    }

    public function perPage(): int
    {
        return $this->paginatorInfo->getPerPage();
    }

    public function hasMorePages(): bool
    {
        return $this->paginatorInfo->hasMorePages();
    }

    public function paginatorInfo(): PaginatorInfo
    {
        return $this->paginatorInfo;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function map(callable $callback): array
    {
        return array_map($callback, $this->items);
    }

    public function filter(callable $callback): array
    {
        return array_filter($this->items, $callback);
    }

    public function pluck(string $key): array
    {
        return array_map(fn ($item) => $item[$key] ?? $item->$key ?? null, $this->items);
    }

    public function toArray(): array
    {
        return [
            'data' => array_map(
                fn ($item) => $item instanceof BaseResponse ? $item->toArray() : $item,
                $this->items
            ),
            'paginatorInfo' => $this->paginatorInfo->toArray(),
        ];
    }
}
