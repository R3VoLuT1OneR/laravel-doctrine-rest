<?php

namespace Tests\App\Actions\PageComment;

use Pz\LaravelDoctrine\JsonApi\Action\RelationshipsAction;

class ShowRelatedUserRelationship extends ShowRelatedUser
{
    use RelationshipsAction;
}