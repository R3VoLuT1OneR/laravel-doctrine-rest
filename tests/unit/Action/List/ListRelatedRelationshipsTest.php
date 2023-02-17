<?php

namespace Tests\Action\List;

use Illuminate\Support\Facades\Route;
use Pz\LaravelDoctrine\JsonApi\Action\List\ListRelatedRelationships;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Tests\App\Entities\Role;
use Tests\App\Transformers\RoleTransformer;
use Tests\TestCase;

class ListRelatedRelationshipsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::get('/users/{id}/relationships/roles', function (JsonApiRequest $request) {
            return (
                new ListRelatedRelationships(
                    $this->usersRepo(),
                    $this->rolesRepo(),
                    RoleTransformer::create(),
                    'users',
                )
            )
                ->setSearchProperty('name')
                ->setFilterable(['id', 'name'])
                ->dispatch($request);
        });
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->get('/users/1/relationships/roles')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/users/2/relationships/roles')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/users/3/relationships/roles')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->get('/users/1/relationships/roles')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/2/relationships/roles')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/users/3/relationships/roles')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $this->get('/users/1/relationships/roles')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/2/relationships/roles')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/3/relationships/roles')->assertStatus(JsonApiResponse::HTTP_OK);
    }

    public function testListRelatedUserRolesResponse()
    {
        $user = $this->actingAsUser();

        $this->get('/users/'.$user->getId().'/relationships/roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'links' => [
                            'self' => '/roles/2'
                        ]
                    ],
                ]
            ]);

        $user->addRoles(Role::root());
        $this->em()->flush();

        $this->get('/users/'.$user->getId().'/relationships/roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'id' => '1',
                        'type' => 'roles',
                        'links' => [
                            'self' => '/roles/1'
                        ]
                    ],
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'links' => [
                            'self' => '/roles/2'
                        ]
                    ],
                ]
            ]);
    }

    public function testListRelatedUserRolesPaginationAndSorting()
    {
        $user = $this->actingAsUser();
        $user->addRoles(Role::root());
        $user->addRoles(Role::moderator());

        $this->em()->flush();


        $this->get('/users/'.$user->getId().'/relationships/roles?sort=-id')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                    ['id' => '2'],
                    ['id' => '1'],
                ]
            ]);

        $this->get('/users/'.$user->getId().'/relationships/roles?page[number]=2&page[size]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '2'],
                ]
            ]);

        $this->get('/users/'.$user->getId().'/relationships/roles?page[offset]=2&page[limit]=1')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    ['id' => '3'],
                ]
            ]);
    }
}