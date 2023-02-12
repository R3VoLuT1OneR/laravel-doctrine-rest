<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\CreateAction as BaseAction;
use Pz\LaravelDoctrine\Rest\Traits\HandlesAuthorization;

class CreateAction extends BaseAction
{
    use HandlesAuthorization;

    protected function restAbility(): string
    {
        return'restCreate';
    }
}
