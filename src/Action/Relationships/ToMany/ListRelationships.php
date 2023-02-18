<?php namespace Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany;

use Pz\LaravelDoctrine\JsonApi\Action\Related\ListRelatedResources;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\RelationshipsAction;

class ListRelationships extends ListRelatedResources
{
    use RelationshipsAction;
}
