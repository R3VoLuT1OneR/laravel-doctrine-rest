<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;
class UnprocessableException extends JsonApiException
{
    const ERROR_CODE = 'unprocessable';
    const ERROR_MESSAGE = 'Unprocessable error.';
    public function __construct(\Exception $exception = null)
    {
        parent::__construct(static::ERROR_MESSAGE, static::HTTP_UNPROCESSABLE_ENTITY, $exception);
    }
}
