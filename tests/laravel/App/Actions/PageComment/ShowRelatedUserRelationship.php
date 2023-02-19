<?php

namespace Tests\App\Actions\PageComment;

use Pz\LaravelDoctrine\JsonApi\Action\RelationshipsActionTrait;

class ShowRelatedUserRelationship extends ShowRelatedUser
{
    use RelationshipsActionTrait;
}