<?php

namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToOne;

use Pz\LaravelDoctrine\JsonApi\Action\Relationships\RelationshipsAction;
use Pz\LaravelDoctrine\JsonApi\Action\Related\ShowRelatedResource;

class ShowRelationship extends ShowRelatedResource
{
    use RelationshipsAction;
}
