<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\RelationshipsTransformer;

trait RelationshipsAction
{
    public function transformer(): AbstractTransformer
    {
        return new RelationshipsTransformer(parent::transformer());
    }
}