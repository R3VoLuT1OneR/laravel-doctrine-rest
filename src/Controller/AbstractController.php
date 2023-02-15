<?php namespace Pz\LaravelDoctrine\JsonApi\Controller;

use Illuminate\Routing\Controller;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\Create\CreateResource;
use Pz\LaravelDoctrine\JsonApi\Action\Remove\RemoveResource;
use Pz\LaravelDoctrine\JsonApi\Action\List\ListResource;
use Pz\LaravelDoctrine\JsonApi\Action\Show\ShowResource;
use Pz\LaravelDoctrine\JsonApi\Action\Update\UpdateResource;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;

abstract class AbstractController extends Controller
{
    abstract protected function transformer(): AbstractTransformer;
    abstract protected function repository(): ResourceRepository;

    /**
     * Param that can be filtered if query is string.
     */
    protected function getFilterProperty(): ?string
    {
        return null;
    }

    /**
     * Get list of filterable entity properties.
     */
    protected function getFilterable(): array
    {
        return [];
    }

    public function index(JsonApiRequest $request): JsonApiResponse
    {
        return (new ListResource($this->repository(), $this->transformer()))
            ->setFilterProperty($this->getFilterProperty())
            ->setFilterable($this->getFilterable())
            ->dispatch($request);
    }

    public function create(JsonApiRequest $request): JsonApiResponse
    {
        return (new CreateResource($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function show(JsonApiRequest $request): JsonApiResponse
    {
        return (new ShowResource($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function update(JsonApiRequest $request): JsonApiResponse
    {
        return (new UpdateResource($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function delete(JsonApiRequest $request): JsonApiResponse
    {
        return (new RemoveResource($this->repository(), $this->transformer()))->dispatch($request);
    }
}
