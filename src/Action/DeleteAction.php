<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\Doctrine\Rest\Action\DeleteAction as BaseAction;
use Pz\LaravelDoctrine\JsonApi\Traits\HandlesAuthorization;

class DeleteAction extends BaseAction
{
    use HandlesAuthorization;

    protected function restAbility(): string
    {
        return 'restDelete';
    }
}
