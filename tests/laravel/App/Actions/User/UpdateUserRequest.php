<?php

namespace Tests\App\Actions\User;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\Facades\Gate;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\Rules\PrimaryDataRule;
use Tests\App\Actions\User\Rules\UserRoleAssignRule;
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
            'data.relationships.roles.data.*' => [new UserRoleAssignRule($this)]
        ]);
    }

}