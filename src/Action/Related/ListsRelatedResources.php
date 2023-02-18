<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Related;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelatedResource;
use Pz\LaravelDoctrine\JsonApi\Action\List\FiltersResource;
use Pz\LaravelDoctrine\JsonApi\Action\List\PaginatesResource;
use Pz\LaravelDoctrine\JsonApi\Action\Related\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

trait ListsRelatedResources
{
    use FiltersResource;
    use PaginatesResource;
    use AuthorizeRelatedResource;
    use RelatedActionTrait;

    /**
     * Creates query builder from related repository and applies condition "related.mappedBy = resource.id".
     */
    protected function relatedQueryBuilder(ResourceInterface $resource): QueryBuilder
    {
        $mappedBy = $this->resourceMappedBy();
        $relatedRepo = $this->relatedResourceRepository();

        return $relatedRepo->resourceQueryBuilder()
            ->innerJoin(sprintf('%s.%s', $relatedRepo->alias(), $mappedBy), $mappedBy)
            ->addCriteria(
                Criteria::create()->andWhere(Criteria::expr()->eq($mappedBy, $resource->getId()))
            );
    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::LIST_RELATED_RESOURCES;
    }
}