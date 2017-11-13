<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\CollectionAction;
use Pz\LaravelDoctrine\Rest\RestRequest;

class IndexAction extends CollectionAction
{
    /**
     * @param RestRequest   $request
     * @param object|string $entity
     */
    public function authorize($request, $entity)
    {
        \Gate::authorize('restIndex', $entity);
    }
}
