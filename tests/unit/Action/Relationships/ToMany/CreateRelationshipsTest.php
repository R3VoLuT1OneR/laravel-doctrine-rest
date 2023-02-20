<?php

namespace Tests\Action\Relationships\ToMany;

use Illuminate\Support\Facades\Route;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany\CreateRelationships;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Tests\App\Actions\User\Relationships\CreateUserRolesRequest;
use Tests\App\Entities\Role;
use Tests\App\Transformers\RoleTransformer;
use Tests\TestCase;

class CreateRelationshipsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::post('/users/{id}/relationships/roles', function (CreateUserRolesRequest $request) {
            return (
                new CreateRelationships(
                    $this->usersRepo(),
                    new RoleTransformer(),
                    $this->rolesRepo(),
                    'roles',
                    'users'
                )
            )
                ->dispatch($request);
        });
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->post('/users/1/relationships/roles')->assertStatus(422);
        $this->post('/users/2/relationships/roles')->assertStatus(422);
        $this->post('/users/3/relationships/roles')->assertStatus(422);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->post('/users/1/relationships/roles')->assertStatus(422);
        $this->post('/users/2/relationships/roles')->assertStatus(422);
        $this->post('/users/3/relationships/roles')->assertStatus(422);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $data = [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ]
        ];

        $this->post('/users/1/relationships/roles', $data)->assertStatus(200);
        $this->post('/users/2/relationships/roles', $data)->assertStatus(200);
        $this->post('/users/3/relationships/roles', $data)->assertStatus(200);
    }

    public function testAssignANewRoleToUser()
    {
        $this->actingAsRoot();

        $response = $this->post('/users/1/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => Role::moderator()],
            ]
        ]);

        $response->assertExactJson([
            'data' => [
                [
                    'id' => '2',
                    'type' => 'roles',
                    'links' => [
                        'self' => '/roles/2'
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'roles',
                    'links' => [
                        'self' => '/roles/3'
                    ],
                ],
            ]
        ]);
    }
}
