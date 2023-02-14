<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\Doctrine\Rest\Action\CollectionAction;
use Pz\LaravelDoctrine\JsonApi\Traits\HandlesAuthorization;

class IndexAction extends CollectionAction
{
    use HandlesAuthorization;

    protected function restAbility(): string
    {
        return 'restIndex';
    }
}
