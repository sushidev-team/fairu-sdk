<?php

use SushiDev\Fairu\Exceptions\FairuException;
use SushiDev\Fairu\Exceptions\AuthenticationException;
use SushiDev\Fairu\Exceptions\ValidationException;
use SushiDev\Fairu\Exceptions\GraphQLException;

describe('FairuException', function () {
    it('creates with message', function () {
        $exception = new FairuException('Something went wrong');

        expect($exception->getMessage())->toBe('Something went wrong');
    });

    it('has default message', function () {
        $exception = new FairuException();

        expect($exception->getMessage())->toBe('An error occurred with the Fairu API');
    });
});

describe('AuthenticationException', function () {
    it('has 401 code by default', function () {
        $exception = new AuthenticationException();

        expect($exception->getCode())->toBe(401);
    });

    it('creates with custom message', function () {
        $exception = new AuthenticationException('Token expired');

        expect($exception->getMessage())->toBe('Token expired');
    });
});

describe('ValidationException', function () {
    it('stores validation errors', function () {
        $errors = [
            'name' => ['The name field is required.'],
            'email' => ['The email must be valid.'],
        ];

        $exception = new ValidationException('Validation failed', $errors);

        expect($exception->getErrors())->toBe($errors);
        expect($exception->getCode())->toBe(422);
    });
});

describe('GraphQLException', function () {
    it('formats multiple errors', function () {
        $errors = [
            ['message' => 'Field not found'],
            ['message' => 'Invalid argument'],
        ];

        $exception = new GraphQLException($errors);

        expect($exception->getMessage())->toContain('Field not found');
        expect($exception->getMessage())->toContain('Invalid argument');
    });

    it('returns first error', function () {
        $errors = [
            ['message' => 'First error'],
            ['message' => 'Second error'],
        ];

        $exception = new GraphQLException($errors);

        expect($exception->getFirstError()['message'])->toBe('First error');
    });

    it('returns all graphql errors', function () {
        $errors = [
            ['message' => 'Error 1'],
            ['message' => 'Error 2'],
        ];

        $exception = new GraphQLException($errors);

        expect($exception->getGraphQLErrors())->toHaveCount(2);
    });

    it('detects validation errors', function () {
        $errors = [
            [
                'message' => 'Validation failed',
                'extensions' => [
                    'category' => 'validation',
                    'validation' => [
                        'name' => ['Required'],
                    ],
                ],
            ],
        ];

        $exception = new GraphQLException($errors);

        expect($exception->hasValidationErrors())->toBeTrue();
        expect($exception->getValidationErrors())->toHaveKey('name');
    });

    it('handles non-validation errors', function () {
        $errors = [
            ['message' => 'Server error'],
        ];

        $exception = new GraphQLException($errors);

        expect($exception->hasValidationErrors())->toBeFalse();
        expect($exception->getValidationErrors())->toBe([]);
    });
});
