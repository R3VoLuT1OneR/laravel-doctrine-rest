<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\List;

use Doctrine\ORM\QueryBuilder;
use Pz\LaravelDoctrine\JsonApi\Action\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResource;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait ListsResources
{
    use AuthorizeResource;
    use FiltersResource;
    use PaginatesResource;

    abstract protected function repository(): ResourceRepository;

    protected function resourceQueryBuilder(): QueryBuilder
    {
        return $this->repository()->resourceQueryBuilder();
    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::LIST_RESOURCES;
    }
}
