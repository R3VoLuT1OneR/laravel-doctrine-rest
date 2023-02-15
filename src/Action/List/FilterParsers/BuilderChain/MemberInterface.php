<?php namespace Pz\LaravelDoctrine\JsonApi\Action\List\FilterParsers\BuilderChain;

/**
 * Member of the builder chain.
 */
interface MemberInterface
{
    /**
     * Chain member receive object that we want to build and need to return object with same type.
     */
    public function __invoke($object);
}
