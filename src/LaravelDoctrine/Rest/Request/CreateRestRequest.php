<?php namespace Pz\LaravelDoctrine\Rest\Request;

use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\LaravelDoctrine\Rest\RestRequest;

abstract class CreateRestRequest extends RestRequest implements CreateRequestInterface
{
    /**
     * @return string
     */
    public function ability()
    {
        return 'create';
    }
}
