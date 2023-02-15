<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\List;

use Pz\LaravelDoctrine\JsonApi\Action\AbstractAction;
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
        $this->applyPagination($qb);
        $this->applyFilter($qb);

        return response()->collection($qb, $this->repository()->getResourceKey(), $this->transformer());
    }
}

