<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Doctrine\ORM\EntityManager;
use League\Fractal\TransformerAbstract;
use Pz\Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionCreateAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionDeleteAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionUpdateAction;
use Pz\Doctrine\Rest\RestRepository;
use Pz\Doctrine\Rest\RestResponse;
use Pz\LaravelDoctrine\Rest\AbstractController;
use Pz\LaravelDoctrine\Rest\RestRequest;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\Role;
use Pz\LaravelDoctrine\Rest\Tests\App\Transformers\RoleTransformer;
use Pz\LaravelDoctrine\Rest\Tests\App\Transformers\UserTransformer;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;

class UserController extends AbstractController
{
    public function __construct(protected EntityManager $em) {}

    protected function getFilterProperty(): string
    {
        return 'email';
    }

    protected function getFilterable(): array
    {
        return ['id', 'email', 'name'];
    }

    protected function transformer(): TransformerAbstract
    {
        return new UserTransformer();
    }

    protected function repository(): RestRepository
    {
        return RestRepository::create($this->em, User::class);
    }

    protected function roles(): RestRepository
    {
        return RestRepository::create($this->em, Role::class);
    }

    public function relatedRoles(RestRequest $request): RestResponse
    {
        $action = new RelatedCollectionAction(
            $this->repository(), 'users',
            RestRepository::create($this->em, Role::class),
            new RoleTransformer()
        );

        return $action->dispatch($request);
    }

    public function relationshipsRolesIndex(RestRequest $request): RestResponse
    {
        return (
            new RelationshipsCollectionAction($this->repository(), 'users', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesCreate(RestRequest $request): RestResponse
    {
        return (
            new RelationshipsCollectionCreateAction($this->repository(), 'roles', 'users', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesUpdate(RestRequest $request): RestResponse
    {
        return (
            new RelationshipsCollectionUpdateAction($this->repository(), 'roles', 'users', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesDelete(RestRequest $request): RestResponse
    {
        return (
            new RelationshipsCollectionDeleteAction($this->repository(), 'roles', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }
}
