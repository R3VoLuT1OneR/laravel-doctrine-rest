<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class ForbiddenException extends JsonApiException
{
    public function __construct(
        $message = 'This action is unauthorized.',
        $httpStatus = self::HTTP_FORBIDDEN,
        \Throwable $previous = null
    )
    {
        parent::__construct($message, $httpStatus, $previous);
    }

    public function errorAtPointer(string $pointer, string $detail): static
    {
        $this->error(static::HTTP_FORBIDDEN, ['pointer' => $pointer], $detail);

        return $this;
    }
}
