<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Mutations;

use SushiDev\Fairu\DTOs\GalleryDTO;
use SushiDev\Fairu\Responses\Gallery;

class GalleryMutations extends BaseMutation
{
    public function create(GalleryDTO $data): ?Gallery
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuGallery($data: FairuGalleryDTO!) {
            createFairuGallery(data: $data) {
                id
                name
                description
                date
                location
                active
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['createFairuGallery'])) {
            return null;
        }

        return new Gallery($result['createFairuGallery']);
    }

    public function update(GalleryDTO $data): ?Gallery
    {
        $mutation = <<<'GRAPHQL'
        mutation UpdateFairuGallery($data: FairuGalleryDTO!) {
            updateFairuGallery(data: $data) {
                id
                name
                description
                date
                location
                active
            }
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['data' => $data->toArray()]);

        if (! isset($result['updateFairuGallery'])) {
            return null;
        }

        return new Gallery($result['updateFairuGallery']);
    }

    public function delete(string $id): bool
    {
        $mutation = <<<'GRAPHQL'
        mutation DeleteFairuGallery($id: ID!) {
            deleteFairuGallery(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['deleteFairuGallery'] ?? false;
    }

    public function createShareLink(string $id): ?string
    {
        $mutation = <<<'GRAPHQL'
        mutation CreateFairuGalleryShareLink($id: ID!) {
            createFairuGalleryShareLink(id: $id)
        }
        GRAPHQL;

        $result = $this->executeMutation($mutation, ['id' => $id]);

        return $result['createFairuGalleryShareLink'] ?? null;
    }
}
