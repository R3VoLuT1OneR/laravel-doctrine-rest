<?php namespace Pz\LaravelDoctrine\JsonApi\Action\List;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\RelationshipsTransformer;

class ListRelatedRelationships extends ListRelatedResources
{
    public function transformer(): AbstractTransformer
    {
        return new RelationshipsTransformer(parent::transformer());
    }
}
