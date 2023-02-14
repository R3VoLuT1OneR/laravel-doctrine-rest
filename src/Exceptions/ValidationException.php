<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class ValidationException extends RestException
{
    const ERROR_CODE = 'validation';
    const ERROR_MESSAGE = 'Validation error.';

    public function __construct(\Exception $exception = null)
    {
        parent::__construct(static::ERROR_MESSAGE, static::HTTP_UNPROCESSABLE_ENTITY, $exception);
    }

    public function validationError(string $pointer, string $detail, array $extra = []): static
    {
        $this->error(static::ERROR_CODE, ['pointer' => $pointer], $detail, $extra);

        return $this;
    }
}
