<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Resource;

use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\CreatesResourceTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

class CreateResources extends AbstractAction
{
    use CreatesResourceTrait;

    public function handle(): JsonApiResponse
    {
        $this->authorize();

        $resources = $this->createResources();

        return response()->collection(
            $resources,
            $this->repository()->getResourceKey(),
            $this->transformer()
        );
    }
}