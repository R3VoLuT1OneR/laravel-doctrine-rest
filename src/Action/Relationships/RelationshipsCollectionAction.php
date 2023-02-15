<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\List\ListRelatedResources;

class RelationshipsCollectionAction extends ListRelatedResources
{
    public function transformer(): AbstractTransformer
    {
        return new RelationshipsTransformer(parent::transformer());
    }
}
