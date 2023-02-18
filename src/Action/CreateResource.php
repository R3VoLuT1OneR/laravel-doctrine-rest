<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\CreatesResource;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

class CreateResource extends AbstractAction
{
    use CreatesResource;

    public function handle(): JsonApiResponse
    {
        $this->authorize();

        $resource = $this->createResource();

        return response()->created($resource, $this->transformer());
    }
}
