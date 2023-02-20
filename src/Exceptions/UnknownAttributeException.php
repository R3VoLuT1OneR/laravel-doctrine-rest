<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class UnknownAttributeException extends JsonApiException
{
    public function __construct(string $pointer)
    {
        parent::__construct(sprintf("Unknown attributes: %s.", $pointer), static::HTTP_BAD_REQUEST);
        $this->error(static::HTTP_BAD_REQUEST, ['pointer' => $pointer], 'Unknown attribute.');
    }
}
