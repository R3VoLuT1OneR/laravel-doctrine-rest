<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\Doctrine\Rest\Action\UpdateAction as BaseAction;
use Pz\LaravelDoctrine\JsonApi\Traits\HandlesAuthorization;

class UpdateAction extends BaseAction
{
    use HandlesAuthorization;

    protected function restAbility(): string
    {
        return 'restUpdate';
    }
}
