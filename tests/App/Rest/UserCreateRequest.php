<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Pz\LaravelDoctrine\Rest\Request\CreateRestRequest;

class UserCreateRequest extends CreateRestRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'password'  => 'required|string|min:6|max:255',
        ];
    }
}
