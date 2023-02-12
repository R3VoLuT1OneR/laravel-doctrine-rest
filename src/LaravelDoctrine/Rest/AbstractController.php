<?php namespace Pz\LaravelDoctrine\Rest;

use Illuminate\Routing\Controller;
use Pz\Doctrine\Rest\AbstractTransformer;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponse;
use Pz\LaravelDoctrine\Rest\Action\CreateAction;
use Pz\LaravelDoctrine\Rest\Action\DeleteAction;
use Pz\LaravelDoctrine\Rest\Action\IndexAction;
use Pz\LaravelDoctrine\Rest\Action\ShowAction;
use Pz\LaravelDoctrine\Rest\Action\UpdateAction;

abstract class AbstractController extends Controller
{
    abstract protected function transformer(): AbstractTransformer;
    abstract protected function repository(): RestRepository;

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

    public function index(RestRequest $request): RestResponse
    {
        return (new IndexAction($this->repository(), $this->transformer()))
            ->setFilterProperty($this->getFilterProperty())
            ->setFilterable($this->getFilterable())
            ->dispatch($request);
    }

    public function create(RestRequest $request): RestResponse
    {
        return (new CreateAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function show(RestRequest $request): RestResponse
    {
        return (new ShowAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function update(RestRequest $request): RestResponse
    {
        return (new UpdateAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function delete(RestRequest $request): RestResponse
    {
        return (new DeleteAction($this->repository(), $this->transformer()))->dispatch($request);
    }
}
