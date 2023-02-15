<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\List;

use Doctrine\ORM\QueryBuilder;
use Pz\LaravelDoctrine\JsonApi\Action\HandlesAuthorization;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait ListsResource
{
    use HandlesAuthorization;
    use FiltersResource;
    use PaginatesResource;

    abstract protected function repository(): ResourceRepository;

    protected function resourceQueryBuilder(): QueryBuilder
    {
        return $this->repository()->resourceQueryBuilder();
    }

    protected function restAbility(): string
    {
        return 'restList';
    }
}
