<?php namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\Doctrine\Rest\Response;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\Traits\HandlesAuthorization;

class ShowAction extends AbstractAction
{
    use HandlesAuthorization;

    public function handle(JsonApiRequest $request): Response
    {
        $resource = $this->repository()->findById($request->getId());

        $this->authorize($resource);

        return response()->item($resource, $this->transformer());
    }

    protected function restAbility(): string
    {
        return 'restShow';
    }
}
