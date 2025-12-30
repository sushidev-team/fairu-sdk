<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Exceptions;

class ValidationException extends FairuException
{
    public function __construct(
        string $message = 'Validation failed',
        private readonly array $errors = [],
        int $code = 422,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
