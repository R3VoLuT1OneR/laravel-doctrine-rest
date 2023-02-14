<?php

namespace Pz\LaravelDoctrine\JsonApi\Exceptions;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ConstraintViolationException extends ValidationException
{
    public function __construct(ConstraintViolationListInterface $errors)
    {
        parent::__construct();

        foreach ($errors as $error) {
            $this->validationError($error->getPropertyPath(), $error->getMessage());
        }
    }
}