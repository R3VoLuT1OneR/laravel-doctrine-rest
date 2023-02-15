<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Show;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\RelationshipsTransformer;

class ShowRelatedRelationship extends ShowRelatedResource
{
    public function transformer(): AbstractTransformer
    {
        return new RelationshipsTransformer(parent::transformer());
    }
}
