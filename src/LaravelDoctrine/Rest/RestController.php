<?php namespace Pz\LaravelDoctrine\Rest;

use Doctrine\ORM\EntityManager;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use League\Fractal\TransformerAbstract;

use pmill\Doctrine\Hydrator\ArrayHydrator;
use Pz\Doctrine\Rest\Action\DeleteAction;
use Pz\Doctrine\Rest\Action\IndexAction;
use Pz\Doctrine\Rest\Action\ShowAction;
use Pz\Doctrine\Rest\Action\CreateAction;
use Pz\Doctrine\Rest\Action\UpdateAction;

use Pz\Doctrine\Rest\RestResponseFactory;
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
     * @var RestResponse|RestResponseFactory
     */
    protected $response;

    /**
     * @return TransformerAbstract
     */
    abstract public function transformer();

    /**
     * UserController constructor.
     *
     * @param EntityManager $em
     * @param RestResponse  $response
     */
    public function __construct(EntityManager $em, RestResponse $response)
    {
        $this->em = $em;
        $this->response = $response;

        $this->middleware(function($request, \Closure $next) {
            try {
                /** @var Response $response */
                $response =  $next($request);

                if (isset($response->exception) && $response->exception instanceof \Exception) {
                    return $this->response()->exception($response->exception);
                }

            } catch (\Exception $e) {
                return $this->response()->exception($e);
            }

            return $response;
        });
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
        return $this->response->transformer($this->transformer());
    }

    /**
     * @return ArrayHydrator
     */
    public function hydrator()
    {
        return new ArrayHydrator($this->em);
    }
}
