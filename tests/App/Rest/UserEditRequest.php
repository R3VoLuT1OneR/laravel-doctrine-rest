<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Pz\LaravelDoctrine\Rest\RestRequest;

class UserEditRequest extends RestRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'name'      => 'string|max:255',
            'email'     => 'email|max:255',
        ], parent::rules());
    }
}
