<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Related;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\ItemAction as BaseItemAction;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Pz\LaravelDoctrine\JsonApi\Response;

class RelatedItemAction extends BaseItemAction
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

    /**
     * @inheritdoc
	*/
	public function handle(): Response
	{
		$resource = $this->repository()->findById($this->request->getId());

        $this->authorize($resource);

        if ($relation = $this->manipulator()->getProperty($resource, $this->field())) {
            return $this->response()->item($relation, $this->transformer());
        }

        return $this->response()->null();
	}
}
