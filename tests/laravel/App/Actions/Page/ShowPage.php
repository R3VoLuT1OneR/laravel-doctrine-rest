<?php

namespace Tests\App\Actions\Page;

use Pz\LaravelDoctrine\JsonApi\Action\Show\ShowResource;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

class ShowPage extends ShowResource
{
    /**
     * Anyone can look on the pages.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}