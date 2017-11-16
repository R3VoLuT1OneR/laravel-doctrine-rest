<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\CreateAction as BaseAction;
use Pz\LaravelDoctrine\Rest\RestRequest;

class CreateAction extends BaseAction
{
    /**
     * @param RestRequest   $request
     * @param string        $entity
     */
    public function authorize($request, $entity)
    {
        \Gate::authorize('restCreate', $entity);
    }
}
