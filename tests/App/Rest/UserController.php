<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Doctrine\ORM\EntityManager;
use Pz\Doctrine\Rest\RestRepository;
use Pz\LaravelDoctrine\Rest\RestController;
use Pz\LaravelDoctrine\Rest\RestRequest;
use Pz\LaravelDoctrine\Rest\Tests\App\Transformers\UserTransformer;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;

class UserController extends RestController
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * UserController constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->transformer = new UserTransformer();
        $this->repository = new RestRepository($em, $em->getClassMetadata(User::class));
    }

    /**
     * @param RestRequest $request
     *
     * @return \Pz\Doctrine\Rest\RestResponse
     */
    public function index(RestRequest $request)
    {
        parent::getFilterProperty();
        parent::getFilterable();
        return parent::index($request);
    }

    /**
     * @return string
     */
    protected function getFilterProperty()
    {
        return 'email';
    }

    /**
     * @return array
     */
    protected function getFilterable()
    {
        return ['id', 'email', 'name'];
    }
}
