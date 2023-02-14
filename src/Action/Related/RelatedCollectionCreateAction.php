<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Related;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Response;

class RelatedCollectionCreateAction extends AbstractAction
{
    use RelatedActionTrait;

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

    public function handle(): Response
    {
        $resource = $this->repository()->findById($this->request->getId());
        $this->authorize($resource);

        foreach ($this->request->getData() as $index => $raw) {
            $item = $this->manipulator()->hydrateResource($this->related()->getClassName(), $raw, "/data/$index");
            $this->manipulator()->addRelationItem($resource, $this->field(), $item);
            $this->validateResource($item);
            $this->related()->em()->persist($item);
        }

        $this->repository()->em()->flush();

        return (
            new RelatedIndexAction(
                $this->repository(),
                $this->mappedBy(),
                $this->related(),
                $this->transformer()
            )
        )
            ->dispatch($this->request)
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
