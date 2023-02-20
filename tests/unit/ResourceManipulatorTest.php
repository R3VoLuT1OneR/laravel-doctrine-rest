<?php

namespace Tests;

use Pz\LaravelDoctrine\JsonApi\Exceptions\JsonApiException;
use Pz\LaravelDoctrine\JsonApi\ResourceManipulator;
use Tests\App\Entities\User;

class ResourceManipulatorTest extends TestCase
{
    public function testHydrateAttributesAndRelationships()
    {
        /** @var User $user */
        $user = $this->manipulator()->hydrateResource(new User(), [
            'attributes'=> [
                'name' => 'TestName',
                'email' => 'test@test.com',
            ],
            'relationships' => [
                'roles' => [
                    'data' => [
                        ['type' => 'roles', 'id' => 2],
                    ]
                ],
            ]
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('TestName', $user->getName());
        $this->assertEquals('test@test.com', $user->getEmail());
        $this->assertEquals('User', $user->getRoles()->first()->getName());
    }

    public function testHydrateExceptions()
    {
        try {
            $this->manipulator()->hydrateResource(new User(), ['attributes' => ['not_exists' => 1 ]]);
            $this->fail('Exception should be thrown.');
        } catch (JsonApiException $e) {
            $this->assertEquals([
                [
                    'code' => '400',
                    'source' => ['pointer' => '/data/attributes/not_exists'],
                    'detail' => 'Unknown attribute.',
                ]
            ], $e->errors());
        }

        try {
            $this->manipulator()->hydrateResource(new User(), ['relationships' => ['not_exists' => 1 ]]);
            $this->fail('Exception should be thrown.');
        } catch (JsonApiException $e) {
            $this->assertEquals([
                [
                    'code' => '400',
                    'source' => ['pointer' => '/data/relationships/not_exists'],
                    'detail' => 'Unknown relationship.',
                ]
            ], $e->errors());
        }

        try {
            $this->manipulator()->hydrateResource(new User(), ['relationships' => ['roles' => 1 ]]);
            $this->fail('Exception should be thrown.');
        } catch (JsonApiException $e) {
            $this->assertEquals([
                [
                    'code' => '400',
                    'source' => ['pointer' => '/data/relationships/roles'],
                    'detail' => 'Data is missing or not an array on pointer level.',
                ]
            ], $e->errors());
        }

        try {
            $this->manipulator()->setProperty(new User(), 'not_exists', 'test');
            $this->fail('Exception should be thrown.');
        } catch (JsonApiException $e) {
            $this->assertEquals([
                [
                    'code' => '400',
                    'source' => [
                        'setter' => User::class.'::setNot_exists',
                    ],
                    'detail' => 'Missing field setter.',
                ]
            ], $e->errors());
        }
    }

    protected function manipulator(): ResourceManipulator
    {
        return new ResourceManipulator($this->em);
    }
}
