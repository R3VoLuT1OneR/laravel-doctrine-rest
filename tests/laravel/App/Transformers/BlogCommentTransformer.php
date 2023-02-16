<?php namespace Tests\App\Transformers;

use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Tests\App\Entities\Blog;
use Tests\App\Entities\BlogComment;
use Tests\App\Entities\User;

class BlogCommentTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [
        'user',
        'blog',
    ];

    public function transform(BlogComment $comment): array
    {
        return [
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
        ];
    }

    public function includeBlog(BlogComment $comment): Item
    {
        return $this->item($comment->getBlog(), new BlogTransformer(), Blog::getResourceKey());
    }

    public function includeUser(BlogComment $comment): Item
    {
        return $this->item($comment->getUser(), new UserTransformer(), User::getResourceKey());
    }
}
