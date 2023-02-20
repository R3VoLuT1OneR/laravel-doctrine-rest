<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class UnknownRelationException extends JsonApiException
{
    public function __construct(string $pointer)
    {
        parent::__construct(sprintf("Unknown relation %s.", $pointer), static::HTTP_BAD_REQUEST);
        $this->error(400, ['pointer' => $pointer], 'Unknown relationship.');
    }
}
