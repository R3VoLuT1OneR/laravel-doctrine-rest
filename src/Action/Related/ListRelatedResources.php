<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Related;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\AbstractAction;
use Pz\LaravelDoctrine\JsonApi\Action\ListsRelatedResourcesTrait;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

/**
* Action for providing collection (list or array) of data with API.
*/
class ListRelatedResources extends AbstractAction
{
    use ListsRelatedResourcesTrait;

    public function __construct(
        ResourceRepository           $repository,
        protected ResourceRepository $relatedResourceRepository,
        AbstractTransformer          $transformer,
        protected string             $resourceMappedBy,
    ) {
        parent::__construct($repository, $transformer);
    }

    public function handle(): JsonApiResponse
    {
        $resource = $this->repository()->findById($this->request()->getId());

        $this->authorize($resource);

        $qb = $this->relatedQueryBuilder($resource);
        $this->applyFilter($qb);
        $this->applyPagination($qb);

        return response()->query($qb,
            $this->relatedResourceRepository()->getResourceKey(),
            $this->transformer()
        );
    }
}
