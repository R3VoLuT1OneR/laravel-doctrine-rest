<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Show;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeRelatedResource;
use Pz\LaravelDoctrine\JsonApi\Action\RelatedActionTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceManipulator;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

class ShowRelatedResource extends AbstractAction
{
    use AuthorizeRelatedResource;

	public function handle(): JsonApiResponse
	{
		$comment = $this->repository()->findById($this->request->getId());

        $this->authorize($comment);

        // /comment/1
        if ($author = $comment->getAuthor()) {
            $this->authorize('canAccesTheAuthor', $author);
            return response()->item($author, $this->transformer());
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
