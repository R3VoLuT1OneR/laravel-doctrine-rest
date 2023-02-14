<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Doctrine\ORM\EntityManager;
use Pz\Doctrine\Rest\AbstractTransformer;
use Pz\Doctrine\Rest\Action\Related\RelatedCollectionAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionCreateAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionDeleteAction;
use Pz\Doctrine\Rest\Action\Relationships\RelationshipsCollectionUpdateAction;
use Pz\Doctrine\Rest\ResourceRepository;
use Pz\Doctrine\Rest\Response;
use Pz\LaravelDoctrine\JsonApi\AbstractController;
use Pz\LaravelDoctrine\JsonApi\RestRequest;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\Role;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;
use Pz\LaravelDoctrine\Rest\Tests\App\Transformers\RoleTransformer;
use Pz\LaravelDoctrine\Rest\Tests\App\Transformers\UserTransformer;

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

    protected function transformer(): AbstractTransformer
    {
        return new UserTransformer();
    }

    protected function repository(): ResourceRepository
    {
        return ResourceRepository::create($this->em, User::class);
    }

    protected function roles(): ResourceRepository
    {
        return ResourceRepository::create($this->em, Role::class);
    }

    public function relatedRoles(RestRequest $request): Response
    {
        $action = new RelatedCollectionAction(
            $this->repository(), 'users',
            ResourceRepository::create($this->em, Role::class),
            new RoleTransformer()
        );

        return $action->dispatch($request);
    }

    public function relationshipsRolesIndex(RestRequest $request): Response
    {
        return (
            new RelationshipsCollectionAction($this->repository(), 'users', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesCreate(RestRequest $request): Response
    {
        return (
            new RelationshipsCollectionCreateAction($this->repository(), 'roles', 'users', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesUpdate(RestRequest $request): Response
    {
        return (
            new RelationshipsCollectionUpdateAction($this->repository(), 'roles', 'users', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesDelete(RestRequest $request): Response
    {
        return (
            new RelationshipsCollectionDeleteAction($this->repository(), 'roles', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }
}
