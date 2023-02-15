<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\List;

use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Pz\LaravelDoctrine\JsonApi\FilterParsers\ArrayFilterParser;
use Pz\LaravelDoctrine\JsonApi\FilterParsers\BuilderChain\CriteriaChain;
use Pz\LaravelDoctrine\JsonApi\FilterParsers\SearchFilterParser;

trait FiltersResource
{
    /**
     * Field that can be filtered if filter is string.
     */
    protected ?string $filterProperty = null;

    /**
     * Get list of filterable entity fields.
     */
    protected array $filterable = [];

    public function setFilterProperty(string $property): static
    {
        $this->filterProperty = $property;
        return $this;
    }

    public function setFilterable(array $filterable): static
    {
        $this->filterable = $filterable;
        return $this;
    }

    /**
     * Apply filter criteria on the query builder.
     * @throws QueryException
     */
    protected function applyFilter(QueryBuilder $qb): static
    {
        $qb->addCriteria(
            CriteriaChain::create($this->filterParsers())->process()
        );

        return $this;
    }

    protected function filterParsers(): array
    {
        return [
            new SearchFilterParser($this->request, $this->filterProperty),
            new ArrayFilterParser($this->request, $this->filterable),
        ];
    }
}
