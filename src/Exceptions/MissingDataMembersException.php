<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class MissingDataMembersException extends RestException
{
    const ERROR_CODE = 'missing-data-members';
    const ERROR_MESSAGE = 'Missing or not array `/data/attributes` or `/data/relationships` at pointer level.';

    public function __construct(string $pointer)
    {
        parent::__construct(static::ERROR_MESSAGE, static::HTTP_BAD_REQUEST);
        $this->error(static::ERROR_CODE, ['pointer' => $pointer], static::ERROR_MESSAGE);
    }
}
