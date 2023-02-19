<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelatedTrait;
use Pz\LaravelDoctrine\JsonApi\Action\RelationshipsActionTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;

use Doctrine\Common\Collections\ArrayCollection;

class UpdateRelationships extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelatedTrait;
    use RelationshipsActionTrait;

    public function __construct(
        ResourceRepository $repository,
        AbstractTransformer $transformer,
        protected ResourceRepository $relatedResourceRepository,
        protected string $relatedFieldName,
        protected string $resourceMappedBy,
    ) {
        parent::__construct($repository, $transformer);
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        $relationships = new ArrayCollection();
        foreach ($this->request()->getData() as $index => $relatedPrimaryData) {
            $relatedResource = $this
                ->relatedResourceRepository()
                ->findByPrimaryData($relatedPrimaryData, "/data/$index");

            $relationships->add($relatedResource);
        }

        $this->manipulator()->setProperty($resource, $this->relatedFieldName(), $relationships);
        $this->repository()->em()->flush();

        return response()->collection(
            $relationships->toArray(),
            $this->relatedResourceRepository()->getResourceKey(),
            $this->transformer()
        );
    }

    public function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::UPDATE_RELATED_RELATIONSHIPS;
    }
}
