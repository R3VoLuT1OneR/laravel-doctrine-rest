<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\List\ListRelatedResources;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResource;

class RelationshipsCollectionCreateAction extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeResource;

    public function __construct(
        ResourceRepository $repository,
        string $field,
        string $mappedBy,
        ResourceRepository $related,
        AbstractTransformer $transformer
    ) {
        parent::__construct($repository, $transformer);
        $this->resourceMappedBy = $mappedBy;
        $this->relatedResourceRepository = $related;
        $this->relatedFieldName = $field;
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request->getId());

        $this->authorize();

        foreach ($this->request->getData() as $raw) {
            $item = $this->findRelatedResource($raw);
            $this->manipulator()->addRelationItem($resource, $this->relatedFieldName(), $item);
        }

        $this->repository()->em()->flush($resource);

        return (
            new ListRelatedResources(
                $this->repository(),
                $this->resourceMappedBy(),
                $this->relatedResourceRepository(),
                $this->transformer()
            )
        )->dispatch($this->request)->setStatusCode(JsonApiResponse::HTTP_CREATED);
    }

    protected function resourceAccessAbility(): string
    {
        return 'restRelationshipsCollectionCreate';
    }

    public function allowed(?ResourceInterface $resource = null): bool
    {
        return $this->gate()->allows($this->resourceAccessAbility(), [
            $resource,
            $this->relatedResourceRepository()->getClassName()
        ]);
    }
}
