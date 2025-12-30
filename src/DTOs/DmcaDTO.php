<?php

declare(strict_types=1);

namespace SushiDev\Fairu\DTOs;

class DmcaDTO extends BaseDTO
{
    public function id(string $id): self
    {
        $this->data['id'] = $id;

        return $this;
    }

    public function name(?string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function email(?string $email): self
    {
        $this->data['email'] = $email;

        return $this;
    }

    public function reply(?string $reply): self
    {
        $this->data['reply'] = $reply;

        return $this;
    }

    public function replySend(bool $send): self
    {
        $this->data['reply_send'] = $send;

        return $this;
    }
}
