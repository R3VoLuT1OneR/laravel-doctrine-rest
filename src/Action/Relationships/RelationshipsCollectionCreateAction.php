<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\Related\RelatedListResource;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\Action\HandlesAuthorization;

class RelationshipsCollectionCreateAction extends AbstractAction
{
    use RelatedActionTrait;
    use HandlesAuthorization;

    public function __construct(
        ResourceRepository $repository,
        string $field,
        string $mappedBy,
        ResourceRepository $related,
        AbstractTransformer $transformer
    ) {
        parent::__construct($repository, $transformer);
        $this->mappedBy = $mappedBy;
        $this->related = $related;
        $this->field = $field;
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request->getId());

        $this->authorize();

        foreach ($this->request->getData() as $raw) {
            $item = $this->getRelatedEntity($raw);
            $this->manipulator()->addRelationItem($resource, $this->field(), $item);
        }

        $this->repository()->em()->flush($resource);

        return (
            new RelatedListResource(
                $this->repository(),
                $this->mappedBy(),
                $this->related(),
                $this->transformer()
            )
        )->dispatch($this->request)->setStatusCode(JsonApiResponse::HTTP_CREATED);
    }

    protected function restAbility(): string
    {
        return 'restRelationshipsCollectionCreate';
    }

    public function allowed(?ResourceInterface $resource = null): bool
    {
        return $this->gate()->allows($this->restAbility(), [
            $resource,
            $this->related()->getClassName()
        ]);
    }
}
