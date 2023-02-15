<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Response;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;

class RelationshipsCollectionDeleteAction extends AbstractAction
{
    use RelatedActionTrait;

    public function __construct(
        ResourceRepository $repository,
        string $field,
        ResourceRepository $related,
        AbstractTransformer $transformer
    ) {
        parent::__construct($repository, $transformer);
        $this->relatedResourceRepository = $related;
        $this->relatedFieldName = $field;
    }

    public function handle(): Response
    {
        $resource = $this->repository()->findById($this->request->getId());

        $this->authorize($resource);

        foreach ($this->request->getData() as $removeItem) {
            $item = $this->findRelatedResource($removeItem);
            $this->manipulator()->removeRelationItem($resource, $this->relatedFieldName(), $item);
        }

        $this->repository()->em()->flush();

        return $this->response()->noContent();
    }
}
