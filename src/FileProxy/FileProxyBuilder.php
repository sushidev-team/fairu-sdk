<?php

namespace SushiDev\Fairu\FileProxy;

use SushiDev\Fairu\Enums\FileProxyFit;
use SushiDev\Fairu\Enums\FileProxyFormat;
use SushiDev\Fairu\Enums\VideoVersions;

class FileProxyBuilder
{
    protected string $baseUrl;
    protected string $id;
    protected string $filename;
    protected array $params = [];

    public function __construct(string $baseUrl, string $id, string $filename = 'file')
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->id = $id;
        $this->filename = $filename;
    }

    public static function make(string $baseUrl, string $id, string $filename = 'file'): self
    {
        return new self($baseUrl, $id, $filename);
    }

    /**
     * Set the output width (1-6000 pixels).
     */
    public function width(int $width): self
    {
        $this->params['width'] = max(1, min(6000, $width));

        return $this;
    }

    /**
     * Set the output height (1-6000 pixels).
     */
    public function height(int $height): self
    {
        $this->params['height'] = max(1, min(6000, $height));

        return $this;
    }

    /**
     * Set both width and height.
     */
    public function dimensions(int $width, int $height): self
    {
        return $this->width($width)->height($height);
    }

    /**
     * Set the output quality (1-100, applies to JPEG/WebP only).
     */
    public function quality(int $quality): self
    {
        $this->params['quality'] = max(1, min(100, $quality));

        return $this;
    }

    /**
     * Set the output format.
     */
    public function format(FileProxyFormat $format): self
    {
        $this->params['format'] = $format->value;

        return $this;
    }

    /**
     * Output as JPEG.
     */
    public function jpg(): self
    {
        return $this->format(FileProxyFormat::JPG);
    }

    /**
     * Output as PNG.
     */
    public function png(): self
    {
        return $this->format(FileProxyFormat::PNG);
    }

    /**
     * Output as WebP.
     */
    public function webp(): self
    {
        return $this->format(FileProxyFormat::WEBP);
    }

    /**
     * Set the fit mode.
     */
    public function fit(FileProxyFit $fit): self
    {
        $this->params['fit'] = $fit->value;

        return $this;
    }

    /**
     * Use cover fit mode (scales and crops to fill dimensions).
     */
    public function cover(): self
    {
        return $this->fit(FileProxyFit::COVER);
    }

    /**
     * Use contain fit mode (maintains aspect ratio within dimensions).
     */
    public function contain(): self
    {
        return $this->fit(FileProxyFit::CONTAIN);
    }

    /**
     * Set the focal point for smart cropping.
     *
     * @param  int  $x  X position (0-100%)
     * @param  int  $y  Y position (0-100%)
     * @param  float|null  $zoom  Zoom level (1.0-100.0)
     */
    public function focal(int $x, int $y, ?float $zoom = null): self
    {
        $x = max(0, min(100, $x));
        $y = max(0, min(100, $y));
        $focal = "{$x}-{$y}";

        if ($zoom !== null) {
            $zoom = max(1.0, min(100.0, $zoom));
            $focal .= "-{$zoom}";
        }

        $this->params['focal'] = $focal;

        return $this;
    }

    /**
     * Download the raw/unprocessed file.
     */
    public function raw(): self
    {
        $this->params['raw'] = 'true';

        return $this;
    }

    /**
     * Process SVG files (convert to raster WebP).
     */
    public function processSvg(): self
    {
        $this->params['process_svg'] = 'true';

        return $this;
    }

    /**
     * Set video quality version.
     */
    public function videoVersion(VideoVersions $version): self
    {
        $this->params['version'] = $version->value;

        return $this;
    }

    /**
     * Extract a frame from video at the specified timestamp.
     *
     * @param  string  $timestamp  Format: HH:MM:SS.mmm
     */
    public function timestamp(string $timestamp): self
    {
        $this->params['timestamp'] = $timestamp;

        return $this;
    }

    /**
     * Add HMAC-SHA256 signature for restricted content.
     */
    public function signature(string $signature, string $signatureDate): self
    {
        $this->params['signature'] = $signature;
        $this->params['signature_date'] = $signatureDate;

        return $this;
    }

    /**
     * Add a custom parameter.
     */
    public function param(string $key, mixed $value): self
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Get all configured parameters.
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Build the final URL.
     */
    public function toUrl(): string
    {
        $url = "{$this->baseUrl}/{$this->id}/{$this->filename}";

        if (! empty($this->params)) {
            $url .= '?'.http_build_query($this->params);
        }

        return $url;
    }

    /**
     * Get the URL string.
     */
    public function __toString(): string
    {
        return $this->toUrl();
    }
}
