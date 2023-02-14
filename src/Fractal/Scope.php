<?php

namespace Pz\LaravelDoctrine\JsonApi\Fractal;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;

class Scope extends \League\Fractal\Scope
{
    public function __construct(
        protected JsonApiRequest $request,
        Manager $manager,
        ResourceInterface $resource,
        $scopeIdentifier = null
    ) {
        parent::__construct($manager, $resource, $scopeIdentifier);
    }

    public function request(): JsonApiRequest
    {
        return $this->request;
    }
}
