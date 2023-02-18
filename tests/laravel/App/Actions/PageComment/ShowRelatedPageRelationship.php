<?php

namespace Tests\App\Actions\PageComment;

use Pz\LaravelDoctrine\JsonApi\Action\Relationships\RelationshipsAction;

class ShowRelatedPageRelationship extends ShowRelatedUser
{
    use RelationshipsAction;
}