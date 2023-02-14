<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class InternalServerErrorException extends RestException
{
    const ERROR_CODE = 'internal';
    const ERROR_MESSAGE = 'Internal Server Error';

    public function __construct(\Throwable $exception = null)
    {
        parent::__construct(static::ERROR_MESSAGE, static::HTTP_INTERNAL_SERVER_ERROR, $exception);
        $this->error(static::ERROR_CODE, [], $exception->getMessage());
    }
}
