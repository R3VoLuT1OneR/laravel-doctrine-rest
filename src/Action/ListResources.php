<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\ListsResources;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

/**
 * Action for providing collection (list or array) of data with API.
 */
class ListResources extends AbstractAction
{
    use ListsResources;

    public function handle(): JsonApiResponse
    {
        $this->authorize();

        $qb = $this->resourceQueryBuilder();
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        return response()->query($qb, $this->repository()->getResourceKey(), $this->transformer());
    }
}
