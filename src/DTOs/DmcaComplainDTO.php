<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

class DmcaComplainDTO extends BaseDTO
{
    public function url(string $url): self
    {
        $this->data['url'] = $url;

        return $this;
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function email(string $email): self
    {
        $this->data['email'] = $email;

        return $this;
    }

    public function text(?string $text): self
    {
        $this->data['text'] = $text;

        return $this;
    }
}
