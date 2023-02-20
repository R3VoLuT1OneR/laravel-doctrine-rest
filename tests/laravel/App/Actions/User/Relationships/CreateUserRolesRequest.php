<?php

namespace Tests\App\Actions\User\Relationships;

use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Tests\App\Actions\User\Rules\UserRoleAssignRule;

class CreateUserRolesRequest extends JsonApiRequest
{
    public function dataRules(): array
    {
        return [
            'data' => 'array|required',
            'data.*' => [new UserRoleAssignRule($this)]
        ];
    }
}
