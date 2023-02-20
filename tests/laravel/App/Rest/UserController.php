<?php namespace Tests\App\Rest;

use Doctrine\ORM\EntityManager;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany\ListRelated;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany\ListRelationships;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany\CreateRelationships;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany\RemoveRelationships;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany\UpdateRelationships;
use Pz\LaravelDoctrine\JsonApi\Controller\AbstractController;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\App\Transformers\RoleTransformer;
use Tests\App\Transformers\UserTransformer;

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

    public function relatedRoles(JsonApiRequest $request): JsonApiResponse
    {
        $action = new ListRelated(
            $this->repository(), 'users',
            ResourceRepository::create($this->em, Role::class),
            new RoleTransformer()
        );

        return $action->dispatch($request);
    }

    public function relationshipsRolesIndex(JsonApiRequest $request): JsonApiResponse
    {
        return (
            new ListRelationships($this->repository(), 'users', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesCreate(JsonApiRequest $request): JsonApiResponse
    {
        return (
            new CreateRelationships($this->repository(), 'roles', 'users', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesUpdate(JsonApiRequest $request): JsonApiResponse
    {
        return (
            new UpdateRelationships($this->repository(), 'roles', 'users', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }

    public function relationshipsRolesDelete(JsonApiRequest $request): JsonApiResponse
    {
        return (
            new RemoveRelationships($this->repository(), 'roles', $this->roles(), new RoleTransformer())
        )->dispatch($request);
    }
}
