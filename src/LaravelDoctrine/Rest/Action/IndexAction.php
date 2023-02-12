<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\CollectionAction;
use Pz\LaravelDoctrine\Rest\Traits\HandlesAuthorization;

class IndexAction extends CollectionAction
{
    use HandlesAuthorization;

    protected function restAbility(): string
    {
        return 'restIndex';
    }
}
