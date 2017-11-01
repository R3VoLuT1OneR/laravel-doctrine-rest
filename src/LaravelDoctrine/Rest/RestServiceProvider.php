<?php namespace Pz\LaravelDoctrine\Rest;

use Illuminate\Support\ServiceProvider;

use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\Response\FractalResponse;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\LaravelDoctrine\Rest\Request\DeleteRestRequest;
use Pz\LaravelDoctrine\Rest\Request\IndexRestRequest;
use Pz\LaravelDoctrine\Rest\Request\ShowRestRequest;

class RestServiceProvider extends ServiceProvider
{
    /**
     * Register laravel doctrine rest application.
     */
    public function register()
    {
        $this->registerDefaultRequests();
        $this->registerResponses();
    }

    /**
     * Register application requests.
     */
    protected function registerResponses()
    {
        $this->app->bind(RestResponseFactory::class, RestResponse::class);
        $this->app->bind(RestResponse::class, function() {
            return new RestResponse();
        });
    }

    /**
     * Register default laravel requests.
     */
    protected function registerDefaultRequests()
    {
        $this->app->bind(IndexRequestInterface::class, IndexRestRequest::class);
        $this->app->bind(IndexRestRequest::class, function() {
            return new IndexRestRequest();
        });
        $this->app->bind(ShowRequestInterface::class, ShowRestRequest::class);
        $this->app->bind(ShowRestRequest::class, function() {
            return new ShowRestRequest();
        });
        $this->app->bind(DeleteRequestInterface::class, DeleteRestRequest::class);
        $this->app->bind(DeleteRestRequest::class, function() {
            return new DeleteRestRequest();
        });
    }
}
