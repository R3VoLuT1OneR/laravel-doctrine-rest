<?php

namespace Tests\App\Actions\PageComment;

use Pz\LaravelDoctrine\JsonApi\Action\RelationshipsActionTrait;

class ShowRelatedPageRelationship extends ShowRelatedUser
{
    use RelationshipsActionTrait;
}