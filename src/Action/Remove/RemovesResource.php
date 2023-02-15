<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Remove;

use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\Action\HandlesAuthorization;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait RemovesResource
{
    use HandlesAuthorization;

    abstract protected function repository(): ResourceRepository;

    public function deleteResource(ResourceInterface $resource): void
    {
        $this->repository()->em()->remove($resource);
        $this->repository()->em()->flush();
    }

    protected function restAbility(): string
    {
        return 'restRemove';
    }
}
