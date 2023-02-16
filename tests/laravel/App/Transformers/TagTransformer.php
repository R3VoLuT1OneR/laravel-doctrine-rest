<?php namespace Tests\App\Transformers;

use League\Fractal\TransformerAbstract;
use Tests\App\Entities\Tag;

class TagTransformer extends TransformerAbstract
{
    public function transform(Tag $tag): array
    {
        return [
            'id' => $tag->getId(),
            'name' => $tag->getName(),
        ];
    }
}
