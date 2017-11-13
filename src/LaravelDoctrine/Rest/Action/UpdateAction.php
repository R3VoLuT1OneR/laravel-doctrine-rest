<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\UpdateAction as BaseAction;
use Pz\LaravelDoctrine\Rest\RestRequest;

class UpdateAction extends BaseAction
{
    /**
     * @param RestRequest   $request
     * @param object|string $entity
     */
    public function authorize($request, $entity)
    {
        \Gate::authorize('restUpdate', $entity);
    }
}
