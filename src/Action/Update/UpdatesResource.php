<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Update;

use Pz\LaravelDoctrine\JsonApi\Action\HandlesAuthorization;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceManipulator;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait UpdatesResource
{
    use HandlesAuthorization;

    abstract protected function repository(): ResourceRepository;
    abstract protected function manipulator(): ResourceManipulator;
    abstract protected function request(): JsonApiRequest;

    public function updateResource(ResourceInterface $resource): ResourceInterface
    {
        $resource = $this->hydrateResource($resource);

        $this->updating($resource);
        $this->repository()->em()->flush();
        $this->updated($resource);

        return $resource;
    }

    protected function hydrateResource(ResourceInterface $resource): ResourceInterface
    {
        $resource = $this->manipulator()->hydrateResource($resource, $this->request()->getData());
        return $resource;
    }

    protected function updating(ResourceInterface $resource): void
    {

    }

    protected function updated(ResourceInterface $resource): void
    {

    }

    protected function restAbility(): string
    {
        return 'restUpdate';
    }
}
