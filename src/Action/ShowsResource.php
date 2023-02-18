<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResource;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

/**
 * Provides helpers needed for implementation of show action.
 */
trait ShowsResource
{
    use AuthorizeResource;

    abstract protected function repository(): ResourceRepository;

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }
}
