<?php

namespace Tests\App\Actions\PageComment;

use Pz\LaravelDoctrine\JsonApi\Action\Show\ShowRelatedResource;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

class ShowRelatedUser extends ShowRelatedResource
{
    /**
     * Anyone can see who wrote the comment.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}