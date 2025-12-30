# Fairu SDK for Laravel

A comprehensive Laravel SDK for the [Fairu](https://fairu.app) GraphQL API with dynamic fragments, caching, and full type support.

## Requirements

- PHP 8.1+
- Laravel 10.0+ or 11.0+

## Installation

```bash
composer require sushidev/fairu-sdk
```

The package will auto-register its service provider and facade.

### Publish Configuration

```bash
php artisan vendor:publish --tag=fairu-config
```

## Configuration

Add the following to your `.env` file:

```env
FAIRU_URL=https://fairu.app
FAIRU_TOKEN=your-api-token
```

### Full Configuration Options

```php
// config/fairu.php
return [
    'base_url' => env('FAIRU_URL', 'https://fairu.app'),
    'token' => env('FAIRU_TOKEN'),
    'timeout' => 30,

    'retry' => [
        'times' => 3,
        'sleep' => 100,
    ],

    'cache' => [
        'enabled' => true,
        'store' => null, // null = default cache store
        'prefix' => 'fairu_',
        'ttl' => [
            'tenant' => 3600,
            'roles' => 3600,
            'assets' => 300,
            'default' => 600,
        ],
    ],
];
```

## Basic Usage

### Queries

```php
use SushiDev\Fairu\Facades\Fairu;

// Health Check
$status = Fairu::health()->check();

// Get single asset
$asset = Fairu::assets()->find('uuid');
$asset = Fairu::assets()->findByPath('/images/logo.png');

// Get multiple assets
$assets = Fairu::assets()->findMany(['uuid-1', 'uuid-2']);

// Search assets
$results = Fairu::assets()->search('logo', page: 1, perPage: 20);

// List assets in folder
$assets = Fairu::assets()->all(folderId: 'folder-uuid', page: 1, perPage: 20);

// Get folder content (folders + assets)
$content = Fairu::folders()->content('folder-uuid');
$content->folders; // Array of Folder objects
$content->assets;  // Array of Asset objects

// Get tenant info
$tenant = Fairu::tenant()->get();

// List galleries
$galleries = Fairu::galleries()->all(['tenant-uuid'], from: '2024-01-01');

// Other queries
$copyrights = Fairu::copyrights()->all();
$licenses = Fairu::licenses()->all();
$workflows = Fairu::workflows()->all();
$users = Fairu::users()->all();
$roles = Fairu::roles()->all();
$disks = Fairu::disks()->all();
```

### Mutations

```php
use SushiDev\Fairu\Facades\Fairu;
use SushiDev\Fairu\DTOs\FileDTO;
use SushiDev\Fairu\DTOs\FolderDTO;
use SushiDev\Fairu\DTOs\GalleryDTO;
use SushiDev\Fairu\Enums\UploadType;

// Upload a file
$uploadLink = Fairu::uploads()->createLink(
    filename: 'photo.jpg',
    type: UploadType::STANDARD,
    folderId: 'folder-uuid',
    alt: 'Photo description'
);
// Use $uploadLink->getUrl() to upload your file via PUT request

// Update asset metadata
$asset = Fairu::assetMutations()->update(
    FileDTO::make()
        ->id('asset-uuid')
        ->alt('New alt text')
        ->description('Updated description')
        ->copyrightIds(['copyright-uuid'])
);

// Delete asset
Fairu::assetMutations()->delete('asset-uuid');

// Move asset to another folder
Fairu::assetMutations()->move('asset-uuid', 'new-folder-uuid');

// Create folder
$folder = Fairu::folderMutations()->create(
    FolderDTO::make()
        ->name('New Folder')
        ->parent('parent-uuid')
);

// Create gallery
$gallery = Fairu::galleryMutations()->create(
    GalleryDTO::make()
        ->name('Event 2024')
        ->folderId('folder-uuid')
        ->date(now())
        ->location('Berlin')
);
```

## Fragments

Fragments allow you to customize which fields are returned from the API.

### Predefined Fragments

Each resource type has three predefined variants: `minimal`, `default`, and `full`.

```php
use SushiDev\Fairu\Facades\Fairu;

// Use predefined fragments
$asset = Fairu::assets()->find('uuid', Fairu::fragments()->asset('minimal'));
$asset = Fairu::assets()->find('uuid', Fairu::fragments()->asset('full'));
```

### Custom Fragments with FragmentBuilder

```php
use SushiDev\Fairu\Fragments\FragmentBuilder;

// Build custom fragment
$fragment = FragmentBuilder::for('FairuAsset')
    ->select(['id', 'name', 'mime', 'url', 'width', 'height', 'blurhash'])
    ->with('copyrights', fn($f) => $f->select(['id', 'name', 'email']))
    ->with('licenses', fn($f) => $f->select(['id', 'name', 'type', 'start', 'end']))
    ->build();

$asset = Fairu::assets()->find('uuid', $fragment);

// With arguments (e.g., for URL parameters)
$fragment = FragmentBuilder::for('FairuAsset')
    ->select(['id', 'name'])
    ->withArguments('url', ['width' => 800, 'height' => 600, 'quality' => 80], [])
    ->build();
```

### Register Custom Fragments

```php
// In a service provider
Fairu::fragments()->register('my_asset_card',
    FragmentBuilder::for('FairuAsset')
        ->select(['id', 'name', 'url', 'blurhash', 'width', 'height'])
        ->build()
);

// Use it later
$asset = Fairu::assets()->find('uuid', Fairu::fragments()->get('my_asset_card'));
```

## Caching

The SDK supports Laravel's cache system for API responses.

```php
// Enable caching for a query (uses config TTL)
$tenant = Fairu::tenant()->cached()->get();

// Custom TTL (in seconds)
$roles = Fairu::roles()->cached(ttl: 3600)->all();

// Force fresh data (bypass cache)
$tenant = Fairu::tenant()->fresh()->get();

// Clear cached data
Fairu::tenant()->forget('cache-key');
```

## DTOs (Data Transfer Objects)

All input types have fluent DTOs for type-safe data handling.

```php
use SushiDev\Fairu\DTOs\FileDTO;
use SushiDev\Fairu\DTOs\FolderDTO;
use SushiDev\Fairu\DTOs\CopyrightDTO;
use SushiDev\Fairu\DTOs\LicenseDTO;
use SushiDev\Fairu\DTOs\GalleryDTO;
use SushiDev\Fairu\DTOs\DiskDTO;
use SushiDev\Fairu\DTOs\DiskCredentialsDTO;
use SushiDev\Fairu\Enums\DiskType;
use SushiDev\Fairu\Enums\LicenseType;

// File DTO
$file = FileDTO::make()
    ->id('uuid')
    ->name('photo.jpg')
    ->alt('Description')
    ->caption('Caption text')
    ->description('Full description')
    ->focalPoint('50-50')
    ->copyrightIds(['cr-1', 'cr-2'])
    ->licenseIds(['lic-1']);

// Copyright DTO
$copyright = CopyrightDTO::make()
    ->name('John Doe Photography')
    ->email('john@example.com')
    ->phone('+1234567890')
    ->website('https://johndoe.com')
    ->active(true);

// License DTO
$license = LicenseDTO::make()
    ->name('Annual License')
    ->type(LicenseType::PERIOD)
    ->copyrightId('copyright-uuid')
    ->start(now())
    ->end(now()->addYear())
    ->days(365);

// Disk DTO with credentials
$credentials = DiskCredentialsDTO::make()
    ->key('aws-access-key')
    ->secret('aws-secret')
    ->bucket('my-bucket')
    ->region('eu-west-1');

$disk = DiskDTO::make()
    ->name('S3 Backup')
    ->type(DiskType::S3)
    ->folderId('folder-uuid')
    ->credentials($credentials)
    ->active(true);
```

## Response Objects

All API responses are wrapped in typed response objects.

```php
$asset = Fairu::assets()->find('uuid');

// Access properties
$asset->id;
$asset->name;
$asset->mime;
$asset->url;
$asset->width;
$asset->height;
$asset->blurhash;

// Helper methods
$asset->isImage();      // true for image/* mime types
$asset->isVideo();      // true for video/* mime types
$asset->isPdf();        // true for application/pdf
$asset->getAspectRatio(); // width/height ratio

// Nested relations
$asset->getCopyrights(); // Array of Copyright objects
$asset->getLicenses();   // Array of License objects

// Array access
$asset['name'];
$asset['url'];

// JSON serialization
json_encode($asset);
```

### Paginated Lists

```php
$results = Fairu::assets()->search('logo');

// Access items
$results->items();       // Array of Asset objects
$results->first();       // First item
$results->last();        // Last item
$results->isEmpty();     // Check if empty
$results->count();       // Items on current page

// Pagination info
$results->total();       // Total items across all pages
$results->currentPage(); // Current page number
$results->lastPage();    // Last page number
$results->perPage();     // Items per page
$results->hasMorePages(); // Has more pages?

// Iteration
foreach ($results as $asset) {
    echo $asset->name;
}

// Collection-like methods
$ids = $results->pluck('id');
$filtered = $results->filter(fn($a) => $a->isImage());
$mapped = $results->map(fn($a) => $a->name);
```

## Enums

All GraphQL enums are available as PHP 8.1 backed enums.

```php
use SushiDev\Fairu\Enums\UploadType;
use SushiDev\Fairu\Enums\SortingDirection;
use SushiDev\Fairu\Enums\LicenseType;
use SushiDev\Fairu\Enums\WorkflowStatus;
use SushiDev\Fairu\Enums\WorkflowType;
use SushiDev\Fairu\Enums\UserStatus;
use SushiDev\Fairu\Enums\DiskType;
use SushiDev\Fairu\Enums\WebhookType;
use SushiDev\Fairu\Enums\CustomDomainStatus;
use SushiDev\Fairu\Enums\GallerySortingField;
use SushiDev\Fairu\Enums\VideoVersions;
use SushiDev\Fairu\Enums\DmcaStatus;
use SushiDev\Fairu\Enums\UploadShareLinkExpiration;
use SushiDev\Fairu\Enums\PdfSignatureRequestStatus;

// Usage
$type = UploadType::STANDARD;
$direction = SortingDirection::DESC;

// From string
$status = WorkflowStatus::from('PROCESSING');
$status = WorkflowStatus::tryFrom('INVALID'); // null
```

## Error Handling

```php
use SushiDev\Fairu\Exceptions\FairuException;
use SushiDev\Fairu\Exceptions\AuthenticationException;
use SushiDev\Fairu\Exceptions\GraphQLException;

try {
    $asset = Fairu::assets()->find('uuid');
} catch (AuthenticationException $e) {
    // Invalid or missing token (401)
} catch (GraphQLException $e) {
    // GraphQL errors
    $errors = $e->getGraphQLErrors();
    $first = $e->getFirstError();

    if ($e->hasValidationErrors()) {
        $validation = $e->getValidationErrors();
    }
} catch (FairuException $e) {
    // General API errors
}
```

## Events

The SDK dispatches events for debugging and logging.

```php
use SushiDev\Fairu\Events\QueryExecuted;
use SushiDev\Fairu\Events\MutationExecuted;

// In EventServiceProvider
protected $listen = [
    QueryExecuted::class => [
        LogQueryListener::class,
    ],
    MutationExecuted::class => [
        LogMutationListener::class,
    ],
];

// Listener
class LogQueryListener
{
    public function handle(QueryExecuted $event)
    {
        Log::debug('Fairu Query', [
            'query' => $event->query,
            'variables' => $event->variables,
            'response' => $event->response,
        ]);
    }
}
```

## Multipart Uploads

For large files, use multipart uploads.

```php
// Initialize multipart upload
$init = Fairu::uploads()->initMultipart(
    filename: 'large-video.mp4',
    folderId: 'folder-uuid',
    fileSize: 104857600, // 100MB
    contentType: 'video/mp4'
);

$fileId = $init->getId();
$uploadId = $init->getUploadId();

// Get upload URL for each part
$parts = [];
for ($i = 1; $i <= $totalParts; $i++) {
    $partInfo = Fairu::uploads()->getMultipartPartUrl($fileId, $uploadId, $i);

    // Upload part to $partInfo['url'] via PUT
    // Collect ETag from response
    $parts[] = [
        'partNumber' => $i,
        'etag' => $etag,
    ];
}

// Complete upload
$result = Fairu::uploads()->completeMultipart($fileId, $uploadId, $parts);

// Or abort if needed
Fairu::uploads()->abortMultipart($fileId, $uploadId);
```

## Testing

```bash
composer test
```

## License

MIT License. See [LICENSE](LICENSE) for details.
