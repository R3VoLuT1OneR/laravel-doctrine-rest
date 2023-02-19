<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResourceTrait;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait RemovesResourceTrait
{
    use AuthorizeResourceTrait;

    abstract protected function repository(): ResourceRepository;

    public function removeResource(ResourceInterface $resource): void
    {
        $this->repository()->em()->remove($resource);
        $this->repository()->em()->flush();
    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::REMOVE_RESOURCE;
    }
}
