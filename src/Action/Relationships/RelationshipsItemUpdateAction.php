<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\Related\RelatedActionTrait;
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
        $this->related = $related;
        $this->field = $field;
    }

    public function handle(): Response
    {
        $resource = $this->repository()->findById($this->request->getId());

        $this->authorize($resource);

        $item = $this->getRelatedEntity($this->request->getData());

        $this->manipulator()->setProperty($resource, $this->field(), $item);

        $this->repository()->em()->flush($resource);

        return (
            new RelationshipsItemAction(
                $this->repository(),
                $this->field(),
                $this->related(),
                $this->transformer()
            )
        )->dispatch($this->request);
    }
}
