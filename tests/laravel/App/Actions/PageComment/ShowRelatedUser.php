<?php

namespace Tests\App\Actions\PageComment;

use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToOne\ShowRelated;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

class ShowRelatedUser extends ShowRelated
{
    /**
     * Anyone can see who wrote the comment.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}
