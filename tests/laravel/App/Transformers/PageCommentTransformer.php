<?php namespace Tests\App\Transformers;

use League\Fractal\Resource\Item;
use Pz\LaravelDoctrine\JsonApi\AbstractTransformer;
use Tests\App\Entities\Page;
use Tests\App\Entities\PageComment;
use Tests\App\Entities\User;

class PageCommentTransformer extends AbstractTransformer
{
    protected array $availableIncludes = [
        'user',
        'page',
    ];

    public function transform(PageComment $comment): array
    {
        return [
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
        ];
    }

    public function includePages(PageComment $comment): Item
    {
        return $this->item($comment->getPage(), new PagesTransformer(), Page::getResourceKey());
    }

    public function includeUser(PageComment $comment): Item
    {
        return $this->item($comment->getUser(), new UserTransformer(), User::getResourceKey());
    }
}
