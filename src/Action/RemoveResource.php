<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\RemovesResource;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

class RemoveResource extends AbstractAction
{
    use RemovesResource;

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);
        $this->removeResource($resource);

        return response()->noContent();
    }
}
