<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Doctrine\Common\Collections\ArrayCollection;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResourceTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceManipulator;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait CreatesResourceTrait
{
    use AuthorizeResourceTrait;

    abstract protected function repository(): ResourceRepository;
    abstract protected function manipulator(): ResourceManipulator;
    abstract protected function request(): JsonApiRequest;

    protected function createResource(): ResourceInterface
    {
        $data = $this->request()->getData();
        $resource = $this->hydrateResource($data);

        $this->creating($resource);
        $this->em()->persist($resource);
        $this->em()->flush();
        $this->created($resource);

        return $resource;
    }

    protected function createResources(): array
    {
        $data = $this->request()->getData();
        $createdResources = [];

        foreach ($data as $index => $itemData) {
            $createdResources[] = $createdResource = $this->hydrateResource($itemData, "/data/$index");
            $this->em()->persist($createdResource);
        }

        $this->creating($createdResources);
        $this->em()->flush();
        $this->created($createdResources);

        return $createdResources;
    }

    protected function hydrateResource(array $data, string $scope = '/data'): ResourceInterface
    {
        $class = $this->repository()->getClassName();
        $resource = $this->manipulator()->hydrateResource(new $class, $data, $scope);
        return $resource;
    }

    protected function creating(ResourceInterface|array $resource): void
    {

    }

    protected function created(ResourceInterface|array $resource): void
    {

    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::CREATE_RESOURCE;
    }
}
