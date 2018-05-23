<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\DeleteAction as BaseAction;
use Pz\LaravelDoctrine\Rest\RestRequest;
use Pz\LaravelDoctrine\Rest\Traits\HandlesAuthorization;

class DeleteAction extends BaseAction
{
    use HandlesAuthorization;

    /**
     * @return string
     */
    protected function restAbility()
    {
        return 'restDelete';
    }
}
