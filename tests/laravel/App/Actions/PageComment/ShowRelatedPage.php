<?php

namespace Tests\App\Actions\PageComment;

use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToOne\ShowRelated;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

class ShowRelatedPage extends ShowRelated
{
    /**
     * Anyone can the page of the comment.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}
