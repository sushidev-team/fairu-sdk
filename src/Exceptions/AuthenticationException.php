<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Exceptions;

class AuthenticationException extends FairuException
{
    public function __construct(
        string $message = 'Authentication failed',
        int $code = 401,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
