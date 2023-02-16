<?php namespace Tests\App\Transformers;

use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Tests\App\Entities\Blog;
use Tests\App\Entities\User;

class BlogTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'user',
    ];

    public function transform(Blog $blog): array
    {
        return [
            'id' => $blog->getId(),
            'title' => $blog->getTitle(),
            'content' => $blog->getContent(),
        ];
    }

    public function includeUser(Blog $blog): Item
    {
        return $this->item($blog->getUser(), new UserTransformer(), User::getResourceKey());
    }
}
