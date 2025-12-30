<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Facades;

use Illuminate\Support\Facades\Facade;
use SushiDev\Fairu\FairuClient;
use SushiDev\Fairu\Queries\AssetQueries;
use SushiDev\Fairu\Queries\CopyrightQueries;
use SushiDev\Fairu\Queries\DiskQueries;
use SushiDev\Fairu\Queries\DmcaQueries;
use SushiDev\Fairu\Queries\FolderQueries;
use SushiDev\Fairu\Queries\GalleryQueries;
use SushiDev\Fairu\Queries\HealthQueries;
use SushiDev\Fairu\Queries\LicenseQueries;
use SushiDev\Fairu\Queries\RoleQueries;
use SushiDev\Fairu\Queries\TenantQueries;
use SushiDev\Fairu\Queries\UserQueries;
use SushiDev\Fairu\Queries\WorkflowQueries;

/**
 * @method static HealthQueries health()
 * @method static AssetQueries assets()
 * @method static FolderQueries folders()
 * @method static GalleryQueries galleries()
 * @method static CopyrightQueries copyrights()
 * @method static LicenseQueries licenses()
 * @method static WorkflowQueries workflows()
 * @method static UserQueries users()
 * @method static RoleQueries roles()
 * @method static DiskQueries disks()
 * @method static DmcaQueries dmcas()
 * @method static TenantQueries tenant()
 * @method static \SushiDev\Fairu\Mutations\UploadMutations uploads()
 * @method static \SushiDev\Fairu\Fragments\FragmentRegistry fragments()
 * @method static \SushiDev\Fairu\FileProxy\FileProxy fileProxy()
 * @method static array query(string $query, array $variables = [])
 * @method static array mutate(string $mutation, array $variables = [])
 *
 * @see \SushiDev\Fairu\FairuClient
 */
class Fairu extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FairuClient::class;
    }
}
