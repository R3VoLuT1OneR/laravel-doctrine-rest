<?php

namespace Tests\App\Actions\Page;

use Pz\LaravelDoctrine\JsonApi\Action\Resource\ShowResource as BasicAction;
use Pz\LaravelDoctrine\JsonApi\ResourceInterface;

class ShowPageResource extends BasicAction
{
    /**
     * Anyone can look on the pages.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}