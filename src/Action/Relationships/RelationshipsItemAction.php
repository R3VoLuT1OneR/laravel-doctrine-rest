<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\Related\RelatedItemAction;

class RelationshipsItemAction extends RelatedItemAction
{
    public function transformer(): AbstractTransformer|callable
    {
        return new RelationshipsTransformer(parent::transformer());
    }
}
