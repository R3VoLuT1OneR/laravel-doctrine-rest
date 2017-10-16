<?php namespace Pz\LaravelDoctrine\Rest;

use Pz\Doctrine\Rest\Response\FractalResponse;
use Pz\Doctrine\Rest\RestRequestInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestResponse extends FractalResponse
{
    /**
     * @param RestRequestInterface $request
     * @throws NotFoundHttpException
     * @return void
     */
    public function notFound(RestRequestInterface $request)
    {
        throw new NotFoundHttpException();
    }
}
