<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Responses;

/**
 * @property-read string $id
 * @property-read string|null $name
 * @property-read string|null $mime
 * @property-read string|null $alt
 * @property-read string|null $caption
 * @property-read string|null $description
 * @property-read string|null $copyright_text
 * @property-read string|null $url
 * @property-read int|null $width
 * @property-read int|null $height
 * @property-read int|null $original_width
 * @property-read int|null $original_height
 * @property-read string|null $blurhash
 * @property-read string|null $focal_point
 * @property-read bool|null $blocked
 * @property-read bool|null $has_error
 * @property-read int|null $size
 * @property-read array|null $versions
 * @property-read string|null $created_at
 * @property-read string|null $updated_at
 */
class Asset extends BaseResponse
{
    public function getId(): string
    {
        return $this->data['id'];
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function getMime(): ?string
    {
        return $this->data['mime'] ?? null;
    }

    public function getUrl(): ?string
    {
        return $this->data['url'] ?? null;
    }

    public function isImage(): bool
    {
        $mime = $this->getMime();

        return $mime && str_starts_with($mime, 'image/');
    }

    public function isVideo(): bool
    {
        $mime = $this->getMime();

        return $mime && str_starts_with($mime, 'video/');
    }

    public function isPdf(): bool
    {
        return $this->getMime() === 'application/pdf';
    }

    public function getCopyrights(): array
    {
        $copyrights = $this->data['copyrights'] ?? [];

        return array_map(fn ($c) => new Copyright($c), $copyrights);
    }

    public function getLicenses(): array
    {
        $licenses = $this->data['licenses'] ?? [];

        return array_map(fn ($l) => new License($l), $licenses);
    }

    public function getAspectRatio(): ?float
    {
        $width = $this->data['width'] ?? null;
        $height = $this->data['height'] ?? null;

        if ($width && $height) {
            return $width / $height;
        }

        return null;
    }
}
