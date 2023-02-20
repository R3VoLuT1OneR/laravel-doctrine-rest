<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class MissingDataException extends JsonApiException
{
    const ERROR_MESSAGE = 'Data is missing or not an array on pointer level.';

    /**
     * @param string $pointer Pointer to the JSON path in JSON:API format.
     */
    public function __construct(string $pointer)
    {
        parent::__construct(static::ERROR_MESSAGE, static::HTTP_BAD_REQUEST);

        $this->error(static::HTTP_BAD_REQUEST, ['pointer' => $pointer], static::ERROR_MESSAGE);
    }
}
