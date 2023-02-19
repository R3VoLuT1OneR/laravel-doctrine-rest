<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Resource;

use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\ListsResourcesTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

/**
 * Action for providing collection (list or array) of data with API.
 */
class ListResources extends AbstractAction
{
    use ListsResourcesTrait;

    public function handle(): JsonApiResponse
    {
        $this->authorize();

        $qb = $this->resourceQueryBuilder();
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        return response()->query($qb, $this->repository()->getResourceKey(), $this->transformer());
    }
}
