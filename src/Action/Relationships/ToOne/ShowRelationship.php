<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToOne;

use Pz\LaravelDoctrine\JsonApi\Action\RelationshipsActionTrait;
use Pz\LaravelDoctrine\JsonApi\Action\Related\ShowRelatedResource;

class ShowRelationship extends ShowRelatedResource
{
    use RelationshipsActionTrait;
}
