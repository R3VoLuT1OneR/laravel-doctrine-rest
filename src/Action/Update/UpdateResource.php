<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Update;

use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

class UpdateResource extends AbstractAction
{
    use UpdatesResource;

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);
        $this->updateResource($resource);

        return response()->item($resource, $this->transformer());
    }

}
