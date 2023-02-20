<?php

namespace Tests\App\Actions\Page\Relationships;

use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\Rules\PrimaryDataRule;
use Tests\App\Entities\User;

class UpdateUserRelationshipRequest extends JsonApiRequest
{
    public function dataRules(): array
    {
        return [
            'data' => [new PrimaryDataRule(User::class)]
        ];
    }
}
