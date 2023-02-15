<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Show;

use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

/**
 * Example of action to show one single resource.
 *
 * For example:
 *   /user/1
 */
class ShowResource extends AbstractAction
{
    use ShowsResource;

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        return response()->item($resource, $this->transformer());
    }
}
