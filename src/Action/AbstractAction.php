<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\Exceptions\ConstraintViolationException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\InternalServerErrorException;
use Pz\LaravelDoctrine\JsonApi\Exceptions\RestException;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\ResourceManipulator;
use Pz\LaravelDoctrine\JsonApi\ResponseFactory;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

use Symfony\Component\Validator\Validation;
use Throwable;

abstract class AbstractAction
{
    protected JsonApiRequest $request;

    public function __construct(
        protected ResourceRepository $repository,
        protected AbstractTransformer $transformer,
    ) {}

    public function repository(): ResourceRepository
    {
        return $this->repository;
    }

    public function transformer(): AbstractTransformer|callable
    {
        return $this->transformer;
    }

    public function request(): JsonApiRequest
    {
        return $this->request;
    }

    public function dispatch(JsonApiRequest $request): JsonApiResponse
    {
        $this->request = $request;

        try {
            return app()->call([$this, 'handle']);
        } catch (RestException $e) {
            return response()->exception($e);
        }
    }

    protected function manipulator(): ResourceManipulator
    {
        return new ResourceManipulator($this->repository()->em());
    }

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
