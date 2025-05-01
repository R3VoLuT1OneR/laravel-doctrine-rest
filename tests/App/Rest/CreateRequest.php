<?php

namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Pz\LaravelDoctrine\Rest\RestRequest;

class CreateRequest extends RestRequest
{
    public function rules()
    {
        $rules =  parent::rules();
        $rules['data.attributes.email'] = 'required|email';
        $rules['data.attributes.name'] = 'required|string|min:3';
        $rules['data.attributes.password'] = 'required';
        return $rules;
    }

}
