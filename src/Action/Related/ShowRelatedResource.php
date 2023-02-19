<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Related;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelatedTrait;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceManipulator;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

class ShowRelatedResource extends AbstractAction
{
    use RelatedActionTrait;
    use AuthorizeRelatedTrait;

    public function __construct(
        ResourceRepository $repository,
        AbstractTransformer $transformer,
        protected string $relatedFieldName,
        protected ?ResourceManipulator $manipulator = null
    ) {
        parent::__construct($repository, $transformer, $this->manipulator);
    }

	public function handle(): JsonApiResponse
	{
		$resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        if ($relation = $this->manipulator()->getProperty($resource, $this->relatedFieldName())) {
            return response()->item($relation, $this->transformer());
        }

        return response()->null();
	}

    public function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }

    public function relatedResourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RELATED_RESOURCE;
    }
}
