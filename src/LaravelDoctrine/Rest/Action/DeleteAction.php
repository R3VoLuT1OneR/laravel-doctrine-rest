<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\DeleteAction as BaseAction;
use Pz\LaravelDoctrine\Rest\RestRequest;

class DeleteAction extends BaseAction
{
    /**
     * @param RestRequest   $request
     * @param object|string $entity
     */
    public function authorize($request, $entity)
    {
        \Gate::authorize('restDelete', $entity);
    }
}
