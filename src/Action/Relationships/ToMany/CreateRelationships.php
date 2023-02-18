<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelatedResource;
use Pz\LaravelDoctrine\JsonApi\Action\Related\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\RelationshipsAction;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

class CreateRelationships extends AbstractAction
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

        foreach ($this->request()->getData() as $raw) {
            $item = $this->findRelatedResource($raw);
            $this->manipulator()->addRelationItem($resource, $this->relatedFieldName(), $item);
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
        return AbilitiesInterface::CREATE_RELATED_RELATIONSHIPS;
    }
}
