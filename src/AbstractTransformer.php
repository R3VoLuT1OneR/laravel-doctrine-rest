<?php

namespace Pz\LaravelDoctrine\JsonApi;

use League\Fractal\TransformerAbstract;

abstract class AbstractTransformer extends TransformerAbstract
{
    protected function primitive($data, $transformer = null, $resourceKey = null)
    {
        throw new \RuntimeException('Primitive values is not supported.');
    }
}
