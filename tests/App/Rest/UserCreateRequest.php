<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Pz\LaravelDoctrine\Rest\RestRequest;

class UserCreateRequest extends RestRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'password'  => 'required|string|min:6|max:255',
        ], parent::rules());
    }
}
