<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\AbilitiesInterface;
use Pz\LaravelDoctrine\JsonApi\Action\AuthorizeResourceTrait;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

/**
 * Provides helpers needed for implementation of show action.
 */
trait ShowsResourceTrait
{
    use AuthorizeResourceTrait;

    abstract protected function repository(): ResourceRepository;

    protected function resourceAccessAbility(): string
    {
        return AbilitiesInterface::SHOW_RESOURCE;
    }
}
