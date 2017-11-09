<?php namespace Pz\LaravelDoctrine\Rest\Request;

use Pz\Doctrine\Rest\Request\UpdateRequestInterface;
use Pz\LaravelDoctrine\Rest\RestRequest;

class UpdateRestRequest extends RestRequest implements UpdateRequestInterface
{
    /**
     * @return string
     */
    public function ability()
    {
        return 'update';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->route('id');
    }
}
