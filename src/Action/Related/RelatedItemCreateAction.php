<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Related;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Response;

class RelatedItemCreateAction extends AbstractAction
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

        $item = $this->manipulator()->hydrateResource($this->relatedResourceRepository()->getClassName(), $this->request->getData(), "/data");
        $this->relatedResourceRepository()->em()->persist($item);
        $this->manipulator()->setProperty($resource, $this->relatedFieldName(), $item);

        $this->relatedResourceRepository()->em()->flush();

        return $this->response()->created($item, $this->transformer());
    }
}
