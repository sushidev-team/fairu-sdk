<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\Predefined\WorkflowFragments;
use SushiDev\Fairu\Responses\PaginatedList;
use SushiDev\Fairu\Responses\Workflow;

class WorkflowQueries extends BaseQuery
{
    protected string $resourceType = 'workflows';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return WorkflowFragments::get($variant);
    }

    public function find(string $id, ?FragmentInterface $fragment = null): ?Workflow
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuWorkflow(\$id: ID!) {
            fairuWorkflow(id: \$id) {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('workflow', ['id' => $id]);
        $result = $this->executeQuery($query, ['id' => $id], $cacheKey);

        if (! isset($result['fairuWorkflow'])) {
            return null;
        }

        return new Workflow($result['fairuWorkflow']);
    }

    public function all(
        int $page = 1,
        int $perPage = 20,
        ?FragmentInterface $fragment = null
    ): PaginatedList {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query FairuWorkflows(\$page: Int, \$perPage: Int) {
            fairuWorkflows(page: \$page, perPage: \$perPage) {
                data {$selection}
                paginatorInfo {
                    currentPage
                    lastPage
                    perPage
                    total
                    hasMorePages
                }
            }
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('workflows', compact('page', 'perPage'));
        $result = $this->executeQuery($query, compact('page', 'perPage'), $cacheKey);

        return new PaginatedList(
            $result['fairuWorkflows'] ?? [],
            fn ($item) => new Workflow($item)
        );
    }
}
