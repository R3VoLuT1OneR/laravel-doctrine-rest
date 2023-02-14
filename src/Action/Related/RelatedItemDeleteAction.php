<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Related;

use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Response;

class RelatedItemDeleteAction extends AbstractAction
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

        $item = $this->manipulator()->getProperty($resource, $this->field(), "/data/relationships");
        $this->related()->em()->remove($item);
        $this->manipulator()->setProperty($resource, $this->field(), null);

        $this->repository()->em()->flush();

        return $this->response()->noContent();
    }
}
