<?php namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Pz\LaravelDoctrine\Rest\Request\UpdateRestRequest;

class UserEditRequest extends UpdateRestRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name'      => 'string|max:255',
            'email'     => 'email|max:255',
        ];
    }
}
