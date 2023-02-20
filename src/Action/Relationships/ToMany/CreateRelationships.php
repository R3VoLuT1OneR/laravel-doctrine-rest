<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelationshipsTrait;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\Action\RelationshipsActionTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

class CreateRelationships extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelationshipsTrait;
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

        foreach ($this->request()->getData() as $index => $relatedPrimaryData) {
            $relatedResource = $this
                ->relatedResourceRepository()
                ->findByPrimaryData($relatedPrimaryData, "/data/$index");

            $this->manipulator()->addRelationItem(
                $resource,
                $this->relatedFieldName(),
                $relatedResource,
            );
        }

        $this->repository()->em()->flush();

        return response()->collection(
            $this->manipulator()->getProperty($resource, $this->relatedFieldName()),
            $this->relatedResourceRepository()->getResourceKey(),
            $this->transformer()
        );
    }

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::CREATE_RELATIONSHIPS;
    }
}
