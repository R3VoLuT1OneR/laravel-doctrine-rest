<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Remove;

use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResource;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait RemovesResource
{
    use AuthorizeResource;

    abstract protected function repository(): ResourceRepository;

    public function deleteResource(ResourceInterface $resource): void
    {
        $this->repository()->em()->remove($resource);
        $this->repository()->em()->flush();
    }

    protected function resourceAccessAbility(): string
    {
        return 'restRemove';
    }
}
