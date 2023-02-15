<?php namespace Pz\LaravelDoctrine\JsonApi\Action\List;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelatedResource;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

/**
* Action for providing collection (list or array) of data with API.
*/
class ListRelatedResources extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelatedResource;
    use FiltersResource;
    use PaginatesResource;

    public function __construct(
        ResourceRepository           $repository,
        AbstractTransformer          $transformer,
        protected ResourceRepository $relatedResourceRepository,
        protected string             $resourceMappedBy,
    ) {
        parent::__construct($repository, $transformer);
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        $qb = $this->relatedQueryBuilder($resource);
        $this->applyPagination($qb);
        $this->applyFilter($qb);

        return response()->collection($qb,
            $this->relatedResourceRepository()->getResourceKey(),
            $this->transformer()
        );
    }

    /**
     * Creates query builder from related repository and applies condition "related.mappedBy = resource.id".
     */
    public function relatedQueryBuilder(ResourceInterface $resource): QueryBuilder
    {
        $mappedBy = $this->resourceMappedBy();
        $relatedRepo = $this->relatedResourceRepository();

        return $relatedRepo->resourceQueryBuilder()
            ->innerJoin(sprintf('%s.%s', $relatedRepo->alias(), $mappedBy), $mappedBy)
            ->addCriteria(
                Criteria::create()->andWhere(Criteria::expr()->eq($mappedBy, $resource->getId()))
            );
    }

    public function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::LIST_RELATED_RESOURCES;
    }
}
