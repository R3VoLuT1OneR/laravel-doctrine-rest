<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\UpdateAction as BaseAction;
use Pz\LaravelDoctrine\Rest\Traits\HandlesAuthorization;

class UpdateAction extends BaseAction
{
    use HandlesAuthorization;

    protected function restAbility(): string
    {
        return 'restUpdate';
    }
}
