<?php namespace Pz\LaravelDoctrine\Rest;

use Doctrine\ORM\EntityManager;
use Illuminate\Routing\Controller;
use League\Fractal\TransformerAbstract;

use pmill\Doctrine\Hydrator\ArrayHydrator;
use Pz\Doctrine\Rest\Action\DeleteAction;
use Pz\Doctrine\Rest\Action\IndexAction;
use Pz\Doctrine\Rest\Action\ShowAction;
use Pz\Doctrine\Rest\Action\CreateAction;
use Pz\Doctrine\Rest\Action\UpdateAction;

use Pz\LaravelDoctrine\Rest\Request\CreateRestRequest;
use Pz\LaravelDoctrine\Rest\Request\UpdateRestRequest;

abstract class RestController extends Controller
{
    use IndexAction;
    use ShowAction;
    use DeleteAction;
    use CreateAction;
    use UpdateAction;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @return TransformerAbstract
     */
    abstract public function transformer();

    /**
     * UserController constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param CreateRestRequest $request
     *
     * @return object
     * @throws \Exception
     */
    public function createEntity($request)
    {
        return $this->hydrator()->hydrate(
            $this->repository()->getClassName(),
            $request->validated()
        );
    }

    /**
     * @param UpdateRestRequest $request
     * @param                   $entity
     *
     * @return object
     * @throws \Exception
     */
    public function updateEntity($request, $entity)
    {
        return $this->hydrator()->hydrate(
            $entity,
            $request->validated()
        );
    }

    /**
     * @return RestResponse
     */
    public function response()
    {
        return new RestResponse($this->transformer());
    }

    /**
     * @return ArrayHydrator
     */
    public function hydrator()
    {
        return new ArrayHydrator($this->em);
    }
}
