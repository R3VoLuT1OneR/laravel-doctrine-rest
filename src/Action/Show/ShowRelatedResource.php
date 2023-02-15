<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Show;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\HandlesAuthorization;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

class ShowRelatedResource extends AbstractAction
{
    use RelatedActionTrait;
    use HandlesAuthorization;

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
	public function handle(): JsonApiResponse
	{
		$resource = $this->repository()->findById($this->request->getId());

        $this->authorize($resource);

        if ($relation = $this->manipulator()->getProperty($resource, $this->field())) {
            return response()->item($relation, $this->transformer());
        }

        return response()->null();
	}
}
