<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\Exceptions\ConstraintViolationException;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Symfony\Component\Validator\Validation;

/**
 * If we want to enable and use this trait we need to install  "symfony/validator"
 * @deprecated
 */
trait ValidatesResource
{
    abstract public function repository(): ResourceRepository;

    protected function validateResource(ResourceInterface $resource): ResourceInterface
    {
        $errors = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator()
            ->validate($resource);

        if ($errors->count() > 0) {
            throw new ConstraintViolationException($errors);
        }

        return $resource;
    }
}
