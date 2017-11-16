<?php namespace Pz\LaravelDoctrine\Rest;

use Illuminate\Routing\Controller;
use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\RestRepository;
use Pz\LaravelDoctrine\Rest\Action\CreateAction;
use Pz\LaravelDoctrine\Rest\Action\DeleteAction;
use Pz\LaravelDoctrine\Rest\Action\IndexAction;
use Pz\LaravelDoctrine\Rest\Action\ShowAction;
use Pz\LaravelDoctrine\Rest\Action\UpdateAction;

class RestController extends Controller
{

    /**
     * @var TransformerAbstract
     */
    protected $transformer;

    /**
     * @var RestRepository
     */
    protected $repository;

    /**
     * @param RestRequest $request
     *
     * @return \Pz\Doctrine\Rest\RestResponse
     */
    public function index(RestRequest $request)
    {
        return (new IndexAction($this->repository(), $this->transformer()))
            ->setFilterProperty($this->getFilterProperty())
            ->setFilterable($this->getFilterable())
            ->dispatch($request);
    }

    /**
     * @param RestRequest $request
     *
     * @return \Pz\Doctrine\Rest\RestResponse
     */
    public function create(RestRequest $request)
    {
        return (new CreateAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    /**
     * @param RestRequest $request
     *
     * @return \Pz\Doctrine\Rest\RestResponse
     */
    public function show(RestRequest $request)
    {
        return (new ShowAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    /**
     * @param RestRequest $request
     *
     * @return \Pz\Doctrine\Rest\RestResponse
     */
    public function update(RestRequest $request)
    {
        return (new UpdateAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    /**
     * @param RestRequest $request
     *
     * @return \Pz\Doctrine\Rest\RestResponse
     */
    public function delete(RestRequest $request)
    {
        return (new DeleteAction($this->repository(), $this->transformer()))->dispatch($request);
    }

    /**
     * Param that can be filtered if query is string.
     *
     * @return null|string
     */
    protected function getFilterProperty()
    {
        return null;
    }

    /**
     * Get list of filterable entity properties.
     *
     * @return array
     */
    protected function getFilterable()
    {
        return [];
    }

    /**
     * @return TransformerAbstract
     */
    protected function transformer()
    {
        return $this->transformer;
    }

    /**
     * @return RestRepository
     */
    protected function repository()
    {
        return $this->repository;
    }
}
