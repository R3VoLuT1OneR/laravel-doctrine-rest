<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class UnknownAttributeException extends RestException
{
    const ERROR_CODE = 'unknown-attribute';
    const ERROR_MESSAGE = 'Unknown attribute.';

    public function __construct(string $pointer)
    {
        parent::__construct(static::ERROR_MESSAGE, static::HTTP_BAD_REQUEST);
        $this->error(static::ERROR_CODE, ['pointer' => $pointer], static::ERROR_MESSAGE);
    }
}