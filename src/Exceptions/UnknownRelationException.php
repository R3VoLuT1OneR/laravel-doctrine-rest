<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class UnknownRelationException extends RestException
{
    const ERROR_CODE = 'unknown-relation';
    const ERROR_MESSAGE = 'Unknown relation.';

    public function __construct(string $pointer)
    {
        parent::__construct(static::ERROR_MESSAGE, static::HTTP_BAD_REQUEST);
        $this->error(static::ERROR_CODE, ['pointer' => $pointer], static::ERROR_MESSAGE);
    }
}