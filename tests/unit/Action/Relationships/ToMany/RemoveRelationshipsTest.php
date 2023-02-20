<?php

namespace Tests\Action\Relationships\ToMany;

use Illuminate\Support\Facades\Route;
use Pz\LaravelDoctrine\JsonApi\Action\Relationships\ToMany\RemoveRelationships;
use Tests\App\Actions\User\Relationships\RemoveUserRolesRequest;
use Tests\App\Entities\Role;
use Tests\App\Transformers\RoleTransformer;
use Tests\TestCase;

class RemoveRelationshipsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::delete('/users/{id}/relationships/roles', function (RemoveUserRolesRequest $request) {
            return (
                new RemoveRelationships(
                    $this->usersRepo(),
                    new RoleTransformer(),
                    $this->rolesRepo(),
                    'roles',
                )
            )
                ->dispatch($request);
        });
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $data = ['data' => [
            ['type' => 'roles', 'id' => (string) Role::user()->getId()]
        ]];

        $this->delete('/users/1/relationships/roles', $data)->assertStatus(403);
        $this->delete('/users/2/relationships/roles', $data)->assertStatus(403);
        $this->delete('/users/3/relationships/roles', $data)->assertStatus(403);
    }

    public function testRemoveUserRoleByRootResponse()
    {
        $this->actingAsRoot();

        $data = [
            'data' => [
                ['type' => 'roles', 'id' => (string) Role::moderator()->getId()]
            ]
        ];

        $response = $this->delete('/users/3/relationships/roles', [
            'data' => [
                ['type' => 'roles', 'id' => (string) Role::root()->getId()]
            ]
        ]);

        $response
            ->assertStatus(422)
            ->assertExactJson([
                'errors' => [
                    [
                        'code' => '422',
                        'detail' => 'User don\'t have assigned role "Root"',
                        'source' => [
                            'pointer' => '/data/0'
                        ]
                    ]
                ]
            ]);

        $this->delete('/users/3/relationships/roles', $data)->assertStatus(204);
    }
}
