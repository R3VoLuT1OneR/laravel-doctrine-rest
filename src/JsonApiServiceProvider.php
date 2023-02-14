<?php

namespace Pz\LaravelDoctrine\JsonApi;


use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Pz\LaravelDoctrine\JsonApi\Fractal\JsonApiSerializer;
use Pz\LaravelDoctrine\JsonApi\Fractal\ScopeFactory;

use League\Fractal\Manager as Fractal;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\ResponseFactory as ResponseFactoryContract;

class JsonApiServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->registerResponseFactory();
        $this->registerFractal();
    }

    protected function registerResponseFactory()
    {
        $this->app->singleton(ResponseFactoryContract::class, function ($app) {
            return new ResponseFactory($app[ViewFactoryContract::class], $app['redirect'], $app[Fractal::class]);
        });
    }

    protected function registerFractal(): void
    {
        $this->app->bind(JsonApiSerializer::class, function (Container $app) {
            return new JsonApiSerializer($app[JsonApiRequest::class]);
        });

        $this->app->bind(ScopeFactory::class, function (Container $app) {
            return new ScopeFactory($app[JsonApiRequest::class]);
        });

        $this->app->bind(Fractal::class, function (Container $app) {
            $request = $app[JsonApiRequest::class];
            $fractal = new Fractal($app[ScopeFactory::class]);
            $fractal->setSerializer($app[JsonApiSerializer::class]);

            if ($includes = $request->getInclude()) {
                $fractal->parseIncludes($includes);
            }

            if ($excludes = $request->getExclude()) {
                $fractal->parseExcludes($excludes);
            }

            if ($fields = $request->getFields()) {
                $fractal->parseFieldsets($fields);
            }

            return $fractal;
        });
    }
}