<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class NotFoundException extends RestException
{
    const ERROR_CODE = 'resource-not-found';
    const ERROR_MESSAGE = 'Resource type "%s" and id "%s" is not found.';

    public function __construct(string $id, string $resource)
    {
        $message = sprintf(static::ERROR_MESSAGE, $id, $resource);

        parent::__construct($message, static::HTTP_NOT_FOUND);

        $this->error(static::ERROR_CODE, [], $message);
    }
}
