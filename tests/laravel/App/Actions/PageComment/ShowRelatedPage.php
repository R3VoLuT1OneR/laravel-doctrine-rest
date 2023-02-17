<?php

namespace Tests\App\Actions\PageComment;

use Pz\LaravelDoctrine\JsonApi\Action\Show\ShowRelatedResource;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

class ShowRelatedPage extends ShowRelatedResource
{
    /**
     * Anyone can the page of the comment.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}