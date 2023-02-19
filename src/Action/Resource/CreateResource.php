<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Resource;

use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\CreatesResourceTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

class CreateResource extends AbstractAction
{
    use CreatesResourceTrait;

    public function handle(): JsonApiResponse
    {
        $this->authorize();

        $resource = $this->createResource();

        return response()->created($resource, $this->transformer());
    }
}
