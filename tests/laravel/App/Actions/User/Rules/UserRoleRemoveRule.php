<?php

namespace Tests\App\Actions\User\Rules;

use Illuminate\Support\Facades\Gate;
use Pz\LaravelDoctrine\JsonApi\Exceptions\ForbiddenException;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\Rules\PrimaryDataRule;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class UserRoleRemoveRule extends PrimaryDataRule
{
    public function __construct(protected JsonApiRequest $request)
    {
        parent::__construct(Role::class, [
            \Closure::fromCallable([$this, 'roleAssignedOnUser']),
            // \Closure::fromCallable([$this, 'allowedRemoveRole']),
        ]);
    }

    public function roleAssignedOnUser($attr, Role $role, $fail): void
    {
        if (!$this->user()->getRoles()->contains($role)) {
            $fail(sprintf('User don\'t have assigned role "%s"', $role->getName()));
        }
    }

    protected function user(): User
    {
        return $this->em()->getReference(User::class, $this->request->getId());
    }
}