<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\Show\ShowRelatedResource;

class RelationshipsResourceShow extends ShowRelatedResource
{
    public function transformer(): AbstractTransformer|callable
    {
        return new RelationshipsTransformer(parent::transformer());
    }
}
