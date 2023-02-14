<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\Doctrine\Rest\Action\ItemAction;
use Pz\LaravelDoctrine\JsonApi\Traits\HandlesAuthorization;

class ShowAction extends ItemAction
{
    use HandlesAuthorization;

    protected function restAbility(): string
    {
        return 'restShow';
    }
}
