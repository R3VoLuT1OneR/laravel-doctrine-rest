<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\ItemAction;
use Pz\LaravelDoctrine\Rest\Traits\HandlesAuthorization;

class ShowAction extends ItemAction
{
    use HandlesAuthorization;

    protected function restAbility(): string
    {
        return 'restShow';
    }
}
