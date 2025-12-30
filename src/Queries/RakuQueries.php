<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Queries;

use SushiDev\Fairu\Contracts\FragmentInterface;
use SushiDev\Fairu\Fragments\FragmentBuilder;
use SushiDev\Fairu\Responses\RakuCredential;

class RakuQueries extends BaseQuery
{
    protected string $resourceType = 'raku';

    protected function getDefaultFragment(string $variant = 'default'): FragmentInterface
    {
        return FragmentBuilder::for('FairuRakuCredential')
            ->select([
                'id',
                'name',
                'access_key_id',
                'bucket',
                'permissions',
                'active',
                'last_used_at',
                'expires_at',
                'created_at',
            ]);
    }

    public function credentials(?FragmentInterface $fragment = null): array
    {
        $selection = $this->getFragment($fragment);

        $query = <<<GRAPHQL
        query {
            fairuRakuCredentials {$selection}
        }
        GRAPHQL;

        $cacheKey = $this->cacheManager?->generateKey('raku_credentials', []);
        $result = $this->executeQuery($query, [], $cacheKey);

        return array_map(
            fn ($item) => new RakuCredential($item),
            $result['fairuRakuCredentials'] ?? []
        );
    }
}
