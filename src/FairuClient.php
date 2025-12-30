<?php

declare(strict_types=1);

namespace SushiDev\Fairu;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use SushiDev\Fairu\Cache\CacheManager;
use SushiDev\Fairu\Events\MutationExecuted;
use SushiDev\Fairu\Events\QueryExecuted;
use SushiDev\Fairu\Exceptions\AuthenticationException;
use SushiDev\Fairu\Exceptions\FairuException;
use SushiDev\Fairu\Exceptions\GraphQLException;
use SushiDev\Fairu\Fragments\FragmentRegistry;
use SushiDev\Fairu\Mutations\AssetMutations;
use SushiDev\Fairu\Mutations\CopyrightMutations;
use SushiDev\Fairu\Mutations\DiskMutations;
use SushiDev\Fairu\Mutations\DmcaMutations;
use SushiDev\Fairu\Mutations\FolderMutations;
use SushiDev\Fairu\Mutations\GalleryMutations;
use SushiDev\Fairu\Mutations\LicenseMutations;
use SushiDev\Fairu\Mutations\PdfSignatureMutations;
use SushiDev\Fairu\Mutations\RakuMutations;
use SushiDev\Fairu\Mutations\RoleMutations;
use SushiDev\Fairu\Mutations\TenantMutations;
use SushiDev\Fairu\Mutations\UploadMutations;
use SushiDev\Fairu\Mutations\UserMutations;
use SushiDev\Fairu\Mutations\WorkflowMutations;
use SushiDev\Fairu\Queries\AssetQueries;
use SushiDev\Fairu\Queries\CopyrightQueries;
use SushiDev\Fairu\Queries\DiskQueries;
use SushiDev\Fairu\Queries\DmcaQueries;
use SushiDev\Fairu\Queries\FolderQueries;
use SushiDev\Fairu\Queries\GalleryQueries;
use SushiDev\Fairu\Queries\HealthQueries;
use SushiDev\Fairu\Queries\LicenseQueries;
use SushiDev\Fairu\Queries\RakuQueries;
use SushiDev\Fairu\Queries\RoleQueries;
use SushiDev\Fairu\Queries\TenantQueries;
use SushiDev\Fairu\Queries\UserQueries;
use SushiDev\Fairu\Queries\WorkflowQueries;

class FairuClient
{
    private Client $httpClient;

    private array $queryInstances = [];

    private array $mutationInstances = [];

    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $token,
        private readonly int $timeout = 30,
        private readonly array $retryConfig = [],
        private readonly ?CacheManager $cacheManager = null,
        private readonly ?FragmentRegistry $fragmentRegistry = null,
    ) {
        $this->httpClient = new Client([
            'base_uri' => rtrim($this->baseUrl, '/').'/graphql',
            'timeout' => $this->timeout,
            'headers' => $this->buildHeaders(),
        ]);
    }

    private function buildHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($this->token) {
            $headers['Authorization'] = 'Bearer '.$this->token;
        }

        return $headers;
    }

    public function query(string $query, array $variables = []): array
    {
        return $this->execute($query, $variables, 'query');
    }

    public function mutate(string $mutation, array $variables = []): array
    {
        return $this->execute($mutation, $variables, 'mutation');
    }

    private function execute(string $operation, array $variables, string $type): array
    {
        $attempt = 0;
        $maxAttempts = $this->retryConfig['times'] ?? 1;
        $sleepMs = $this->retryConfig['sleep'] ?? 100;

        while ($attempt < $maxAttempts) {
            try {
                $response = $this->httpClient->post('', [
                    'json' => [
                        'query' => $operation,
                        'variables' => $variables,
                    ],
                ]);

                $body = json_decode($response->getBody()->getContents(), true);

                $this->dispatchEvent($type, $operation, $variables, $body);

                if (isset($body['errors']) && ! empty($body['errors'])) {
                    throw new GraphQLException($body['errors']);
                }

                return $body['data'] ?? [];

            } catch (RequestException $e) {
                $statusCode = $e->getResponse()?->getStatusCode();

                if ($statusCode === 401) {
                    throw new AuthenticationException('Invalid or missing API token');
                }

                $attempt++;
                if ($attempt >= $maxAttempts) {
                    throw new FairuException(
                        'Request failed after '.$maxAttempts.' attempts: '.$e->getMessage(),
                        $e->getCode(),
                        $e
                    );
                }

                usleep($sleepMs * 1000);

            } catch (GuzzleException $e) {
                $attempt++;
                if ($attempt >= $maxAttempts) {
                    throw new FairuException(
                        'Request failed: '.$e->getMessage(),
                        $e->getCode(),
                        $e
                    );
                }

                usleep($sleepMs * 1000);
            }
        }

        throw new FairuException('Request failed after '.$maxAttempts.' attempts');
    }

    private function dispatchEvent(string $type, string $operation, array $variables, array $response): void
    {
        if (! function_exists('event')) {
            return;
        }

        $event = $type === 'query'
            ? new QueryExecuted($operation, $variables, $response)
            : new MutationExecuted($operation, $variables, $response);

        event($event);
    }

    // Query accessors
    public function health(): HealthQueries
    {
        return $this->queryInstances['health'] ??= new HealthQueries($this);
    }

    public function assets(): AssetQueries
    {
        return $this->queryInstances['assets'] ??= new AssetQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function folders(): FolderQueries
    {
        return $this->queryInstances['folders'] ??= new FolderQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function galleries(): GalleryQueries
    {
        return $this->queryInstances['galleries'] ??= new GalleryQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function copyrights(): CopyrightQueries
    {
        return $this->queryInstances['copyrights'] ??= new CopyrightQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function licenses(): LicenseQueries
    {
        return $this->queryInstances['licenses'] ??= new LicenseQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function workflows(): WorkflowQueries
    {
        return $this->queryInstances['workflows'] ??= new WorkflowQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function users(): UserQueries
    {
        return $this->queryInstances['users'] ??= new UserQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function roles(): RoleQueries
    {
        return $this->queryInstances['roles'] ??= new RoleQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function disks(): DiskQueries
    {
        return $this->queryInstances['disks'] ??= new DiskQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function dmcas(): DmcaQueries
    {
        return $this->queryInstances['dmcas'] ??= new DmcaQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function tenant(): TenantQueries
    {
        return $this->queryInstances['tenant'] ??= new TenantQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    public function raku(): RakuQueries
    {
        return $this->queryInstances['raku'] ??= new RakuQueries($this, $this->cacheManager, $this->fragmentRegistry);
    }

    // Mutation accessors
    public function uploads(): UploadMutations
    {
        return $this->mutationInstances['uploads'] ??= new UploadMutations($this);
    }

    public function assetMutations(): AssetMutations
    {
        return $this->mutationInstances['assets'] ??= new AssetMutations($this, $this->cacheManager);
    }

    public function folderMutations(): FolderMutations
    {
        return $this->mutationInstances['folders'] ??= new FolderMutations($this, $this->cacheManager);
    }

    public function galleryMutations(): GalleryMutations
    {
        return $this->mutationInstances['galleries'] ??= new GalleryMutations($this, $this->cacheManager);
    }

    public function copyrightMutations(): CopyrightMutations
    {
        return $this->mutationInstances['copyrights'] ??= new CopyrightMutations($this, $this->cacheManager);
    }

    public function licenseMutations(): LicenseMutations
    {
        return $this->mutationInstances['licenses'] ??= new LicenseMutations($this, $this->cacheManager);
    }

    public function workflowMutations(): WorkflowMutations
    {
        return $this->mutationInstances['workflows'] ??= new WorkflowMutations($this, $this->cacheManager);
    }

    public function userMutations(): UserMutations
    {
        return $this->mutationInstances['users'] ??= new UserMutations($this, $this->cacheManager);
    }

    public function roleMutations(): RoleMutations
    {
        return $this->mutationInstances['roles'] ??= new RoleMutations($this, $this->cacheManager);
    }

    public function diskMutations(): DiskMutations
    {
        return $this->mutationInstances['disks'] ??= new DiskMutations($this, $this->cacheManager);
    }

    public function dmcaMutations(): DmcaMutations
    {
        return $this->mutationInstances['dmcas'] ??= new DmcaMutations($this, $this->cacheManager);
    }

    public function tenantMutations(): TenantMutations
    {
        return $this->mutationInstances['tenant'] ??= new TenantMutations($this, $this->cacheManager);
    }

    public function pdfSignatureMutations(): PdfSignatureMutations
    {
        return $this->mutationInstances['pdfSignature'] ??= new PdfSignatureMutations($this);
    }

    public function rakuMutations(): RakuMutations
    {
        return $this->mutationInstances['raku'] ??= new RakuMutations($this, $this->cacheManager);
    }

    public function fragments(): FragmentRegistry
    {
        return $this->fragmentRegistry ?? new FragmentRegistry();
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getCacheManager(): ?CacheManager
    {
        return $this->cacheManager;
    }
}
