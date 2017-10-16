<?php namespace Pz\LaravelDoctrine\Rest;

use Illuminate\Support\ServiceProvider;

use Pz\Doctrine\Rest\Request\DeleteRequestInterface;
use Pz\Doctrine\Rest\Request\IndexRequestInterface;
use Pz\Doctrine\Rest\Request\ShowRequestInterface;
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
        $this->registerRequests();
    }

    /**
     * Register application requests.
     */
    protected function registerRequests() {}

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
