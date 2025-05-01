<?php

namespace Pz\LaravelDoctrine\Rest\Tests\App\Rest;

use Pz\LaravelDoctrine\Rest\RestRequest;

class UpdateRequest extends RestRequest
{
    public function rules()
    {
        $rules =  parent::rules();
        $rules['data.attributes.email'] = 'email';
        return $rules;
    }

}
