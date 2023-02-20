<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelationshipsTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;

class RemoveRelationships extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelationshipsTrait;

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

        foreach ($this->request()->getData() as $index => $relatedPrimaryData) {
            $relatedResource = $this
                ->relatedResourceRepository()
                ->findByPrimaryData($relatedPrimaryData, "/data/$index");

            $this->manipulator()->removeRelationItem($resource, $this->relatedFieldName(), $relatedResource);
        }

        $this->repository()->em()->flush();

        return response()->noContent();
    }

    public function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::REMOVE_RELATIONSHIPS;
    }
}
