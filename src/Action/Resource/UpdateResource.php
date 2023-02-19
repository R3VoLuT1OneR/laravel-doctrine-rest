<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Resource;

use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\UpdatesResourceTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

class UpdateResource extends AbstractAction
{
    use UpdatesResourceTrait;

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);
        $this->updateResource($resource);

        return response()->item($resource, $this->transformer());
    }
}
