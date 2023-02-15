<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Show;

use Pz\LaravelDoctrine\JsonApi\Action\HandlesAuthorization;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

trait ShowsResource
{
    use HandlesAuthorization;

    abstract protected function repository(): ResourceRepository;

    protected function restAbility(): string
    {
        return 'restShow';
    }
}
