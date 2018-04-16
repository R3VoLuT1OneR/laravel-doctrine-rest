<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Doctrine\ORM\EntityManager;
use Pz\Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionCreateAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionDeleteAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionUpdateAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\LaravelDoctrine\Rest\RestController;
use Pz\LaravelDoctrine\Rest\RestRequest;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\Role;
use Pz\LaravelDoctrine\Rest\Tests\App\Transformers\RoleTransformer;
use Pz\LaravelDoctrine\Rest\Tests\App\Transformers\UserTransformer;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;

class UserController extends RestController
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var RestRepository
     */
    protected $roles;

    /**
     * UserController constructor.
     *
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->transformer = new UserTransformer();
        $this->repository = RestRepository::create($this->em, User::class);
        $this->roles = RestRepository::create($this->em, Role::class);
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
     * @param RestRequest $request
     *
     * @return \Pz\Doctrine\Rest\RestResponse
     */
    public function relatedRoles(RestRequest $request)
    {
        $action = new RelatedCollectionAction(
            $this->repository(), 'users',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );

        return $action->dispatch($request);
    }

    public function relationshipsRolesIndex(RestRequest $request)
    {
        return (
            new RelationshipsCollectionAction($this->repository(), 'users', $this->roles, new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesCreate(RestRequest $request)
    {
        return (
            new RelationshipsCollectionCreateAction($this->repository(), 'roles', 'users', $this->roles, new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesUpdate(RestRequest $request)
    {
        return (
            new RelationshipsCollectionUpdateAction($this->repository(), 'roles', 'users', $this->roles, new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesDelete(RestRequest $request)
    {
        return (
            new RelationshipsCollectionDeleteAction($this->repository(), 'roles', $this->roles, new RoleTransformer())
        )->dispatch($request);
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
