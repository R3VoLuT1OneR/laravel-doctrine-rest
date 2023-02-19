<?php

namespace Tests\App\Actions\Page;

use Pz\LaravelDoctrine\JsonApi\Action\Resource\ShowResource;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

class ShowRelatedComments extends ShowResource
{
    /**
     * Anyone can look on the page's comments.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}