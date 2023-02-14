<?php namespace Pz\LaravelDoctrine\JsonApi\FilterParsers\BuilderChain;

class Chain
{
    protected array $members = [];

    public static function create(array $members = []): static
    {
        return new static($members);
    }

    public function __construct(array $members = [])
    {
        $this->add($members);
    }

    public function add(array|MemberInterface|callable $member): static
    {
        if (is_array($member)) {
            foreach ($member as $item) {
                $this->add($item);
            }

            return $this;
        }

        $this->members[] = $member;

        return $this;
    }

    public function process(mixed $object): mixed
    {
        foreach ($this->members as $member) {
            $object = call_user_func($member, $object);
        }

        return $object;
    }
}
