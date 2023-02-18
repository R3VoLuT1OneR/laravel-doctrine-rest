<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\CreatesResource;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

class CreateResources extends AbstractAction
{
    use CreatesResource;

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