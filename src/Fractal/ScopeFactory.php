<?php

namespace Pz\LaravelDoctrine\JsonApi\Fractal;

use League\Fractal\Manager;
use League\Fractal\Resource\ResourceInterface;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;

class ScopeFactory extends \League\Fractal\ScopeFactory
{
    public function __construct(protected JsonApiRequest $request) {}

    public function request(): JsonApiRequest
    {
        return $this->request;
    }

    public function createScopeFor(Manager $manager, ResourceInterface $resource, $scopeIdentifier = null): Scope
    {
        return new Scope($this->request, $manager, $resource, $scopeIdentifier);
    }
}
