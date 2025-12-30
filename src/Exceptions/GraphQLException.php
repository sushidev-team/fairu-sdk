<?php

declare(strict_types=1);

namespace SushiDev\Fairu\Exceptions;

class GraphQLException extends FairuException
{
    private array $graphqlErrors;

    public function __construct(array $errors, ?\Throwable $previous = null)
    {
        $this->graphqlErrors = $errors;

        $message = $this->formatErrors($errors);
        parent::__construct($message, 0, $previous);
    }

    private function formatErrors(array $errors): string
    {
        $messages = array_map(
            fn (array $error) => $error['message'] ?? 'Unknown error',
            $errors
        );

        return implode('; ', $messages);
    }

    public function getGraphQLErrors(): array
    {
        return $this->graphqlErrors;
    }

    public function getFirstError(): ?array
    {
        return $this->graphqlErrors[0] ?? null;
    }

    public function hasValidationErrors(): bool
    {
        foreach ($this->graphqlErrors as $error) {
            if (isset($error['extensions']['category']) && $error['extensions']['category'] === 'validation') {
                return true;
            }
        }

        return false;
    }

    public function getValidationErrors(): array
    {
        $validationErrors = [];

        foreach ($this->graphqlErrors as $error) {
            if (isset($error['extensions']['validation'])) {
                $validationErrors = array_merge($validationErrors, $error['extensions']['validation']);
            }
        }

        return $validationErrors;
    }
}
