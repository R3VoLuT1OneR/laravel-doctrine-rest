<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Create;

use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResource;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceManipulator;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait CreatesResource
{
    use AuthorizeResource;

    abstract protected function repository(): ResourceRepository;
    abstract protected function manipulator(): ResourceManipulator;
    abstract protected function request(): JsonApiRequest;

    protected function createResource(): ResourceInterface
    {
        $resource = $this->hydrateResource();

        $this->creating($resource);
        $this->repository()->em()->persist($resource);
        $this->repository()->em()->flush();
        $this->created($resource);

        return $resource;
    }

    protected function hydrateResource(): ResourceInterface
    {
        $class = $this->repository()->getClassName();
        $resource = $this->manipulator()->hydrateResource($class, $this->request()->getData());
        return $resource;
    }

    protected function creating(ResourceInterface $resource): void
    {

    }

    protected function created(ResourceInterface $resource): void
    {

    }

    protected function resourceAccessAbility(): string
    {
        return'restCreate';
    }
}
