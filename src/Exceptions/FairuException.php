<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Exceptions;

use Exception;

class FairuException extends Exception
{
    public function __construct(
        string $message = 'An error occurred with the Fairu API',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
