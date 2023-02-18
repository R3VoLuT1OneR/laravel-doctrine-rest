<?php

namespace Tests\App\Actions\User;

use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;

class CreateUserRequest extends JsonApiRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'data.attributes.name' => 'required',
            'data.attributes.password' => 'required',
            'data.attributes.email' => 'required|email',
        ]);
    }
}