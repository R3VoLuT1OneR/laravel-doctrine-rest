<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Doctrine\ORM\QueryBuilder;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResourceTrait;
use Pz\LaravelDoctrine\JsonApi\Action\FiltersResourceTrait;
use Pz\LaravelDoctrine\JsonApi\Action\PaginatesResourceTrait;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait ListsResourcesTrait
{
    use AuthorizeResourceTrait;
    use FiltersResourceTrait;
    use PaginatesResourceTrait;

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
