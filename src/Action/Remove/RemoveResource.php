<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Remove;

use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResource;

class RemoveResource extends AbstractAction
{
    use RemovesResource;

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);
        $this->deleteResource($resource);

        return response()->noContent();
    }
}
