<?php namespace Pz\LaravelDoctrine\JsonApi;

use Illuminate\Routing\Controller;
use Pz\Doctrine\Rest\AbstractTransformer;
use Pz\Doctrine\Rest\ResourceRepository;
use Pz\Doctrine\Rest\Response;
use Pz\LaravelDoctrine\JsonApi\Action\CreateAction;
use Pz\LaravelDoctrine\JsonApi\Action\DeleteAction;
use Pz\LaravelDoctrine\JsonApi\Action\IndexAction;
use Pz\LaravelDoctrine\JsonApi\Action\ShowAction;
use Pz\LaravelDoctrine\JsonApi\Action\UpdateAction;

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

    public function index(RestRequest $request): Response
    {
        return (new IndexAction($this->repository(), $this->transformer()))
            ->setFilterProperty($this->getFilterProperty())
            ->setFilterable($this->getFilterable())
            ->dispatch($request);
    }

    public function create(RestRequest $request): Response
    {
        return (new CreateAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function show(RestRequest $request): Response
    {
        return (new ShowAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function update(RestRequest $request): Response
    {
        return (new UpdateAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function delete(RestRequest $request): Response
    {
        return (new DeleteAction($this->repository(), $this->transformer()))->dispatch($request);
    }
}
