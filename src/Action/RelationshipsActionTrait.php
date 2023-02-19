<?php

namespace Pz\LaravelDoctrine\JsonApi\Action;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\RelationshipsTransformer;

trait RelationshipsActionTrait
{
    public function transformer(): AbstractTransformer
    {
        return new RelationshipsTransformer(parent::transformer());
    }
}