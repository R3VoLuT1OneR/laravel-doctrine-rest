<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Pz\LaravelDoctrine\JsonApi\FilterParsers\BuilderChain\CriteriaChain;
use Pz\LaravelDoctrine\JsonApi\FilterParsers\ArrayFilterParser;
use Pz\LaravelDoctrine\JsonApi\FilterParsers\SearchFilterParser;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;use Pz\LaravelDoctrine\JsonApi\Traits\HandlesAuthorization;

/**
 * Action for providing collection (list or array) of data with API.
 */
class IndexAction extends AbstractAction
{
    use HandlesAuthorization;

    /**
     * Field that can be filtered if filter is string.
     */
    protected ?string $filterProperty = null;

    /**
     * Get list of filterable entity fields.
     */
    protected array $filterable = [];

    public function handle(): JsonApiResponse
    {
        $this->authorize();

        $qb = $this->sourceQueryBuilder();
        $this->applyPagination($qb);
        $this->applyFilter($qb);

        return response()->collection($qb, $this->repository()->getResourceKey(), $this->transformer());
    }

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
     * Param that can be filtered if query is string.
     */
    public function getStringFilterField(): ?string
    {
        return $this->filterProperty;
    }

    /**
     * Get list of filterable entity properties.
     */
    public function getArrayFilterFields(): array
    {
        return $this->filterable;
    }

    protected function restAbility(): string
    {
        return 'restIndex';
    }

    protected function sourceQueryBuilder(): QueryBuilder
    {
        return $this->repository()->sourceQueryBuilder();
    }

    /**
     * Apply pagination criteria to the query builder.
     * @throws QueryException
     */
    protected function applyPagination(QueryBuilder $qb): static
    {
        $qb->addCriteria(
            new Criteria(null,
                $this->request->getSort(),
                $this->request->getFirstResult(),
                $this->request->getMaxResults()
            )
        );

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
            new SearchFilterParser($this->request, $this->getStringFilterField()),
            new ArrayFilterParser($this->request, $this->getArrayFilterFields()),
        ];
    }
}

