<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelationshipsTrait;
use Pz\LaravelDoctrine\JsonApi\Action\FiltersResourceTrait;
use Pz\LaravelDoctrine\JsonApi\Action\PaginatesResourceTrait;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

trait ListsRelatedResourcesTrait
{
    use FiltersResourceTrait;
    use PaginatesResourceTrait;
    use AuthorizeRelationshipsTrait;
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
        return AbilitiesInterface::LIST_RELATIONSHIPS;
    }
}
