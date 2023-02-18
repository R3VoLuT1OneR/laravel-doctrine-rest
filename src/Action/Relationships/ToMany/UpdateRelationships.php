<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelatedResource;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\RelationshipsAction;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Action\Related\RelatedActionTrait;

use Doctrine\Common\Collections\ArrayCollection;

class UpdateRelationships extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelatedResource;
    use RelationshipsAction;

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

        $relationships = new ArrayCollection(array_map(
            fn ($raw) => $this->findRelatedResource($raw, true),
            $this->request()->getData()
        ));

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
