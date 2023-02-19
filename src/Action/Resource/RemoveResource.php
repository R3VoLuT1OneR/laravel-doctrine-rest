<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Resource;

use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\RemovesResourceTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

class RemoveResource extends AbstractAction
{
    use RemovesResourceTrait;

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);
        $this->removeResource($resource);

        return response()->noContent();
    }
}
