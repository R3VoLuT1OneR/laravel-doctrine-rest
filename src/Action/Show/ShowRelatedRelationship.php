<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Show;

use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Pz\LaravelDoctrine\JsonApi\Action\RelationshipsAction;
use Pz\LaravelDoctrine\JsonApi\RelationshipsTransformer;

class ShowRelatedRelationship extends ShowRelatedResource
{
    use RelationshipsAction;
}
