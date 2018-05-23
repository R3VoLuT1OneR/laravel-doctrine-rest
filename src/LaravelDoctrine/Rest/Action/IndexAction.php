<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\CollectionAction;
use Pz\LaravelDoctrine\Rest\RestRequest;
use Pz\LaravelDoctrine\Rest\Traits\HandlesAuthorization;

class IndexAction extends CollectionAction
{
    use HandlesAuthorization;

    /**
     * @return string
     */
    protected function restAbility()
    {
        return 'restIndex';
    }
}
