<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToOne;

use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelatedTrait;
use Pz\LaravelDoctrine\JsonApi\Action\RelationshipsActionTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;

class UpdateRelationship extends AbstractAction
{
     use RelatedActionTrait;
     use AuthorizeRelatedTrait;
     use RelationshipsActionTrait;

    public function __construct(
        ResourceRepository $repository,
        AbstractTransformer $transformer,
        protected ResourceRepository $relatedResourceRepository,
        protected string $relatedFieldName,
    ) {
        parent::__construct($repository, $transformer);
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        if (null === ($data = $this->request()->getData())) {
            $this->manipulator()->setProperty($resource, $this->relatedFieldName(), null);
            return response()->null();
        }

        $relatedResource = $this->findRelatedResource($data);
        $this->manipulator()->setProperty($resource, $this->relatedFieldName(), $relatedResource);
        $this->repository()->em()->flush();

        return response()->item($relatedResource, $this->transformer());
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
