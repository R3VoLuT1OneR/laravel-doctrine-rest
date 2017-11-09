<?php namespace Pz\LaravelDoctrine\Rest;

use Illuminate\Routing\UrlGenerator;
use Pz\Doctrine\Rest\Response\FractalResponse;
use Pz\Doctrine\Rest\RestRequestInterface;
use Pz\LaravelDoctrine\Rest\Request\IndexRestRequest;
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

    /**
     * @param RestRequest $request
     *
     * @return \Closure
     */
    protected function getPaginatorRouteGenerator($request)
    {
        if ($request instanceof IndexRestRequest) {
            $request = clone $request;

            return function(int $page) use ($request) {
                $request->query->set('page', [
                    'offset' => ($page - 1) * $request->getLimit(),
                    'limit' => $request->getLimit(),
                ]);

                return $request->getUri();
            };
        }

        return function() {};
    }

    /**
     * @return UrlGenerator
     */
    private function urlGenerator()
    {
        return app(UrlGenerator::class);
    }
}
