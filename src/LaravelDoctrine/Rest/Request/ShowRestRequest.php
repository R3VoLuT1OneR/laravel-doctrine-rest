<?php namespace Pz\LaravelDoctrine\Rest\Request;

use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\LaravelDoctrine\Rest\RestRequest;

class ShowRestRequest extends RestRequest implements ShowRequestInterface
{
    /**
     * @inheritdoc
     */
    public function ability()
    {
        return 'show';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return (int) $this->route('id');
    }
}
