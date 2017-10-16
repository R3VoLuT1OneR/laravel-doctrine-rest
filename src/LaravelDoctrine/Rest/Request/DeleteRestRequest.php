<?php namespace Pz\LaravelDoctrine\Rest\Request;

use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\LaravelDoctrine\Rest\RestRequest;

class DeleteRestRequest extends RestRequest implements DeleteRequestInterface
{
    /**
     * @return string
     */
    public function ability()
    {
        return 'delete';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->route('id');
    }
}
