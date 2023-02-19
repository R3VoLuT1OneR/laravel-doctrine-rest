<?php

namespace Tests\App\Actions\User;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\Facades\Gate;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\Rules\PrimaryDataRule;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class UpdateUserRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'data.attributes.name' => 'sometimes|required|string',
            'data.attributes.email' => 'sometimes|required|email',
            'data.attributes.password' => 'sometimes|required|string',

            'data.relationships.roles.data' => 'sometimes|array',
            'data.relationships.roles.data.*' => [$this->userRoleUpdateRule()]
        ]);
    }

    protected function userRoleUpdateRule(): PrimaryDataRule
    {
        return new PrimaryDataRule(Role::class, function ($attr, Role $role, $fail) {
            if (!Gate::allows('assignRole', [$this->getUpdatedUser(), $role])) {
                $fail('User not allowed to assign new roles to the user.');
            }
        });
    }

    protected function getUpdatedUser(): User
    {
        return $this->em()->getReference(User::class, $this->getId());
    }

    protected function em(): EntityManager
    {
        return app(EntityManager::class);
    }
}