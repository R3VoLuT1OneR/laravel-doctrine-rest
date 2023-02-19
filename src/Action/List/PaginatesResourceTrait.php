<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\List;

use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;

trait PaginatesResourceTrait
{
    abstract protected function request(): JsonApiRequest;

    /**
     * Apply pagination criteria to the query builder.
     * @throws QueryException
     */
    protected function applyPagination(QueryBuilder $qb): static
    {
        $qb->addCriteria(
            new Criteria(null,
                $this->request()->getSort(),
                $this->request()->getFirstResult(),
                $this->request()->getMaxResults()
            )
        );

        return $this;
    }
}
