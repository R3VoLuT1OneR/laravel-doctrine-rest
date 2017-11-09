<?php namespace Pz\LaravelDoctrine\Rest;

use Illuminate\Support\ServiceProvider;

use Pz\Doctrine\Rest\Request\CreateRequestInterface;
use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;
use Pz\Doctrine\Rest\Request\UpdateRequestInterface;
use Pz\Doctrine\Rest\Response\FractalResponse;
use Pz\Doctrine\Rest\RestResponseFactory;
use Pz\LaravelDoctrine\Rest\Request\CreateRestRequest;
use Pz\LaravelDoctrine\Rest\Request\DeleteRestRequest;
use Pz\LaravelDoctrine\Rest\Request\IndexRestRequest;
use Pz\LaravelDoctrine\Rest\Request\ShowRestRequest;
use Pz\LaravelDoctrine\Rest\Request\UpdateRestRequest;

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
            return new RestResponse($this->app->make('url')->to('/rest'));
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
        $this->app->bind(CreateRequestInterface::class, CreateRestRequest::class);
        $this->app->bind(CreateRestRequest::class, function() {
            return new CreateRestRequest();
        });
        $this->app->bind(UpdateRequestInterface::class, UpdateRestRequest::class);
        $this->app->bind(UpdateRestRequest::class, function() {
            return new UpdateRestRequest();
        });
    }
}
