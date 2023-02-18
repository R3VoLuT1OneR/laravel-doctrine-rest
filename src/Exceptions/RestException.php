<?php namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

class RestException extends \Exception implements RestExceptionInterface
{
    public function __construct(
        $message = '',
        $httpStatus = self::HTTP_INTERNAL_SERVER_ERROR,
        \Throwable  $previous = null,
        protected array $errors = [],
    ) {
        parent::__construct($message, $httpStatus, $previous);
    }

    public static function create(...$args): static
    {
        return new static(...$args);
    }

    public function httpStatus(): int
    {
        return $this->getCode();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function error(string $code, array $source, string $detail, array $extra = []): self
    {
        $this->errors[] = array_merge(['code' => $code, 'source' => $source, 'detail' => $detail] + $extra);

        return $this;
    }
}
