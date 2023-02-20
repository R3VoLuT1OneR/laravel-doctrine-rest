<?php

namespace Tests\FilterParsers;

use PHPUnit\Framework\TestCase;
use Pz\LaravelDoctrine\JsonApi\FilterParsers\BuilderChain\Chain;
use Pz\LaravelDoctrine\JsonApi\FilterParsers\BuilderChain\MemberInterface;

use stdClass;
use Mockery as m;

class ChainTest extends TestCase
{
    public function testChainProcess()
    {
        $object = new stdClass();

        $member1 = m::mock(MemberInterface::class)
            ->shouldReceive('__invoke')->with($object)->andReturn($object)
            ->getMock();
        $member2 = m::mock(MemberInterface::class)
            ->shouldReceive('__invoke')->with($object)->andReturn($object)
            ->getMock();
        $member3 = function($qb) { return $qb; };

        $chain = new Chain([$member1, $member2, $member3]);

        $this->assertEquals($object, $chain->process($object));
    }
}
