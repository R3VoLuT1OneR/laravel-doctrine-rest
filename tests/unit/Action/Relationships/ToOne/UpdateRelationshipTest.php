<?php

namespace Tests\Action\Relationships\ToOne;

use Illuminate\Support\Facades\Route;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToOne\UpdateRelationship;
use Tests\App\Actions\Page\Relationships\UpdateUserRelationshipRequest;
use Tests\App\Transformers\UserTransformer;
use Tests\TestCase;

class UpdateRelationshipTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::patch('/pages/{id}/relationships/user', function (UpdateUserRelationshipRequest $request) {
            return (
                new UpdateRelationship(
                    $this->pageRepo(),
                    new UserTransformer(),
                    $this->usersRepo(),
                    'user'
                )
            )
                ->dispatch($request);
        });
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $data = ['data' => ['type' => 'users', 'id' => '2']];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(403);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();
        $data = ['data' => ['type' => 'users', 'id' => '2']];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(403);
    }

    public function testAuthorizationPermissionsForModeratorRole()
    {
        $this->actingAsModerator();
        $data = ['data' => ['type' => 'users', 'id' => '2']];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(200);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();
        $data = ['data' => ['type' => 'users', 'id' => '2']];

        $this->patch('/pages/1/relationships/user', $data)->assertStatus(200);
    }

    public function testUpdatePageUserRelationshipResponse()
    {
        $this->actingAsModerator();
        $data = ['data' => ['type' => 'users', 'id' => '2']];

        $response = $this->patch('/pages/1/relationships/user', $data);

        $response->assertExactJson([
            'data' => [
                'id' => '2',
                'type' => 'users',
                'relationships' => [
                    'roles' => [
                        'links' => [
                            'related' => '/users/2/roles',
                            'self' => '/users/2/relationships/roles'
                        ]
                    ]
                ],
                'links' => [
                    'self' => '/users/2'
                ]
            ],
        ]);
    }
}
