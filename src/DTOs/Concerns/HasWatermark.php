<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs\Concerns;

trait HasWatermark
{
    /**
     * Set the visible watermark: true on, false off, null inherits.
     *
     * Leave this uncalled to keep the current value. Requires the visible
     * watermark add-on.
     */
    public function watermark(?bool $watermark): self
    {
        $this->data['watermark'] = $watermark;

        return $this;
    }

    /**
     * Keeps an explicit watermark null: the API reads it as "inherit", while
     * an omitted field means "leave it alone".
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (array_key_exists('watermark', $this->data)) {
            $data['watermark'] = $this->data['watermark'];
        }

        return $data;
    }
}
