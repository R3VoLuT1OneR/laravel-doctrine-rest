<?php namespace Pz\LaravelDoctrine\Rest\Action;

use Pz\Doctrine\Rest\Action\ItemAction;
use Pz\LaravelDoctrine\Rest\RestRequest;

class ShowAction extends ItemAction
{
    /**
     * @param RestRequest   $request
     * @param object|string $entity
     */
    public function authorize($request, $entity)
    {
        \Gate::authorize('restShow', $entity);
    }
}
