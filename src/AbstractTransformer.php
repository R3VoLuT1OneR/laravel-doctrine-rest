<?php

namespace Pz\LaravelDoctrine\JsonApi;

use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;

abstract class AbstractTransformer extends TransformerAbstract
{
    public static function create(): static
    {
        return new static();
    }

    protected function primitive($data, $transformer = null, $resourceKey = null): Primitive
    {
        throw new \RuntimeException('Primitive values is not supported.');
    }
}
