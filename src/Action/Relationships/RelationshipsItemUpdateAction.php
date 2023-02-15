<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\Action\Show\ShowRelatedRelationship;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\Response;

class RelationshipsItemUpdateAction extends AbstractAction
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

        $item = $this->findRelatedResource($this->request->getData());

        $this->manipulator()->setProperty($resource, $this->relatedFieldName(), $item);

        $this->repository()->em()->flush($resource);

        return (
            new ShowRelatedRelationship(
                $this->repository(),
                $this->relatedFieldName(),
                $this->relatedResourceRepository(),
                $this->transformer()
            )
        )->dispatch($this->request);
    }
}
