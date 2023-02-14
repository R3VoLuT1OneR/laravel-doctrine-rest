<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\Doctrine\Rest\Action\CreateAction as BaseAction;
use Pz\LaravelDoctrine\JsonApi\Traits\HandlesAuthorization;

class CreateAction extends BaseAction
{
    use HandlesAuthorization;

    protected function restAbility(): string
    {
        return'restCreate';
    }
}
