<?php namespace Pz\LaravelDoctrine\Rest\Tests\Unit;

use Pz\Doctrine\Rest\RestResponse;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\Role;
use Pz\LaravelDoctrine\Rest\Tests\App\Rest\UserController;
use Pz\LaravelDoctrine\Rest\Tests\TestCase;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;

class UserControllerTest extends TestCase
{

    /**
     * @var User
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        Route::group(['prefix' => '/rest'], function() {
            Route::group(['prefix' => '/users'], function() {
                Route::get('', UserController::class . '@index');
                Route::post('', UserController::class . '@create');
                Route::get('/{id}', UserController::class . '@show');
                Route::patch('/{id}', UserController::class . '@update');
                Route::delete('/{id}', UserController::class . '@delete');

                Route::get('/{id}/roles', UserController::class . '@relatedRoles');
                Route::get('/{id}/relationships/roles', UserController::class.'@relationshipsRolesIndex');
                Route::post('/{id}/relationships/roles', UserController::class.'@relationshipsRolesCreate');
                Route::patch('/{id}/relationships/roles', UserController::class.'@relationshipsRolesUpdate');
                Route::delete('/{id}/relationships/roles', UserController::class.'@relationshipsRolesDelete');
            });
        });

        $this->user = $this->em->find(User::class, 1);
        $this->actingAs($this->user);
    }

    public function test_user_related_role()
    {
        $response = $this->get('/rest/users/1/roles');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => '1',
                    'type' => 'role',
                    'attributes' => [
                        'name' => 'Root',
                    ],
                    'links' => [
                        'self' => '/role/1'
                    ]
                ]
            ]
        ]);

        $response = $this->postJson('/rest/users/1/relationships/roles', ['data' => [
            ['id' => Role::USER, 'type' => Role::getResourceKey()]
        ]]);
        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                ['id' => '1'],
                ['id' => '2'],
            ]
        ]);
        $response = $this->get('/rest/users/1/roles');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['id' => '1'],
                ['id' => '2'],
            ]
        ]);

        $response = $this->get('/rest/users/1/relationships/roles');
        $response->assertStatus(200);
        $response->assertExactJson([
            'data' => [
                ['id' => '1', 'type' => Role::getResourceKey(), 'links' => ['self' => '/role/1']],
                ['id' => '2', 'type' => Role::getResourceKey(), 'links' => ['self' => '/role/2']],
            ]
        ]);

        $response = $this->patchJson('/rest/users/1/relationships/roles', ['data' => [
            ['id' => Role::USER, 'type' => Role::getResourceKey()],
        ]]);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['id' => '2'],
            ]
        ]);
        $response = $this->get('/rest/users/1/roles');
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['id' => '2'],
            ]
        ]);

        $response = $this->delete('/rest/users/1/relationships/roles', ['data' => [
            ['id' => 2, 'type' => Role::getResourceKey()]
        ]]);
        $response->assertStatus(204);
        $response = $this->get('/rest/users/1/roles');
        $response->assertStatus(200);
        $response->assertJson(['data' => null]);
    }

    public function test_user_roles_relationship()
    {
        $queryString = http_build_query(['include' => 'roles']);
        $response = $this->get("/rest/users?$queryString");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'id' => 1,
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'self' => '/user/1/relationships/roles',
                                'related' => '/user/1/roles',
                            ],
                            'data' => [
                                [
                                    'id' => Role::ROOT,
                                    'type' => Role::getResourceKey(),
                                ]
                            ]
                        ]
                    ],
                ],
                [
                    'id' => 2,
                    'relationships' => [
                        'roles' => [
                            'data' => [
                                [
                                    'id' => Role::USER,
                                    'type' => Role::getResourceKey(),
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'id' => 3,
                    'relationships' => [
                        'roles' => [
                            'data' => [
                                [
                                    'id' => Role::USER,
                                    'type' => Role::getResourceKey(),
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'id' => Role::ROOT,
                    'type' => Role::getResourceKey(),
                    'attributes' => [
                        'name' => Role::ROOT_NAME,
                    ]
                ],
                [
                    'id' => Role::USER,
                    'type' => Role::getResourceKey(),
                    'attributes' => [
                        'name' => Role::USER_NAME,
                    ]
                ],
            ]
        ]);

        $response = $this->patchJson("/rest/users/1?$queryString", [
            'data' => [
                'relationships' => [
                    'roles' => [
                        'data' => [
                            [
                                'attributes' => [
                                    'name' => 'New Role',
                                ]
                            ],
                        ]
                    ]
                ]
            ]
        ]);
        $response->assertStatus(501);
        $response->assertJson([
            'errors' => [
                [
                    'code' => 'validation',
                    'source' => [
                        'pointer' => 'roles',
                    ],
                    'detail' => "Can't add not persisted new role though User entity."
                ]
            ]
        ]);

        $response = $this->patchJson("/rest/users/2?$queryString", [
            'data' => [
                'relationships' => [
                    'roles' => [
                        'data' => [
                            [
                                'id' => Role::ROOT,
                                'type' => Role::getResourceKey(),
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $response->assertStatus(200);
        $this->assertStringNotContainsString(Role::USER_NAME, $response->getContent());
        $response->assertJson([
            'data' => [
                'id' => 2,
                'type' => User::getResourceKey(),
                'relationships' => [
                    'roles' => [
                        'data' => [
                            [
                                'id' => Role::ROOT,
                                'type' => Role::getResourceKey(),
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        /**
         * Verify that on patch controller we can't modify role name.
         */
        $response = $this->patchJson("/rest/users/2?$queryString", [
            'data' => [
                'relationships' => [
                    'roles' => [
                        'data' => [
                            [
                                'id' => Role::ROOT,
                                'type' => Role::getResourceKey(),
                                'attributes' => [
                                    'name' => 'Change name',
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 2,
                'type' => User::getResourceKey(),
                'relationships' => [
                    'roles' => [
                        'data' => [
                            [
                                'id' => Role::ROOT,
                                'type' => Role::getResourceKey(),
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'id' => Role::ROOT,
                    'type' => Role::getResourceKey(),
                    'attributes' => [
                        'name' => Role::ROOT_NAME,
                    ]
                ]
            ]
        ]);

        $response = $this->postJson("/rest/users?$queryString", [
            'data' => [
                'attributes' => [
                    'name' => 'testing user4',
                    'email' => 'test4email@test.com',
                    'password' => '123456'
                ],
            ]
        ]);
        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'relationships' => [
                    'roles' => [
                        'data' => []
                    ]
                ]
            ]
        ]);

        $response = $this->postJson("/rest/users?$queryString", [
            'data' => [
                'attributes' => [
                    'name' => 'testing user5',
                    'email' => 'test5email@test.com',
                    'password' => '123456'
                ],
                'relationships' => [
                    'roles' => [
                        'data' => [
                            [
                                'id' => Role::USER,
                                'type' => Role::getResourceKey(),
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        $response
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 5,
                    'attributes' => [
                        'name' => 'testing user5',
                        'email' => 'test5email@test.com'
                    ],
                    'relationships' => [
                        'roles' => [
                            'data' => [
                                [
                                    'id' => Role::USER,
                                    'type' => Role::getResourceKey(),
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
        $this->delete('/rest/users/4')->assertStatus(RestResponse::HTTP_NO_CONTENT);
        $this->delete('/rest/users/5')->assertStatus(RestResponse::HTTP_NO_CONTENT);
        $this->get('/rest/users/4')->assertStatus(RestResponse::HTTP_NOT_FOUND);
        $this->get('/rest/users/5')->assertStatus(RestResponse::HTTP_NOT_FOUND);
    }

    public function test_edit_action()
    {
        $this->patchJson('/rest/users/3', ['data' => ['attributes' => ['email' => 'fasdfas']]])
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'code' => 'validation',
                        'source' => [
                            'pointer' => 'email',
                        ],
                        'detail' => 'Please provide correct email address'
                    ],
                ],
            ]);

        $this->patchJson('/rest/users/3', ['data' => ['attributes' => ['email' => 'testEdit@edit.com']]])
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 3,
                    'attributes' => [
                        'name' => 'testing user3',
                        'email' => 'testEdit@edit.com',
                    ],
                ],
            ]);

        $this->get('/rest/users/3')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 3,
                    'attributes' => [
                        'name' => 'testing user3',
                        'email' => 'testEdit@edit.com',
                    ]
                ],
            ]);
    }

    public function test_store_action()
    {
        $response = $this->postJson('/rest/users', ['data' => 'test']);
        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'code' => 'missing-root-data',
                        'source' => [
                            'pointer' => '',
                        ],
                        'detail' => 'Missing `data` member at document top level.'
                    ],
                ]
            ]);

        $response = $this->postJson('/rest/users', ['data' => ['attributes' => []]]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'code' => 'validation',
                        'source' => [
                            'pointer' => 'email',
                        ],
                        'detail' => 'This value should not be null.'
                    ],
                    [
                        'code' => 'validation',
                        'source' => [
                            'pointer' => 'name',
                        ],
                        'detail' => 'This value should not be null.'
                    ],
                    [
                        'code' => 'validation',
                        'source' => [
                            'pointer' => 'password',
                        ],
                        'detail' => 'This value should not be null.'
                    ],
                ],
            ]);

        $response = $this->postJson('/rest/users', ['data' => ['attributes' => ['name' => 'te', 'email' => 'fasdf@test.com']]]);
        $response
            ->assertStatus(422)
            ->assertJson([
                'errors' => [
                    [
                        'code' => 'validation',
                        'source' => [
                            'pointer' => 'name',
                        ],
                        'detail' => 'Name must be at least 3 characters long',
                    ],
                    [
                        'code' => 'validation',
                        'source' => [
                            'pointer' => 'password',
                        ],
                        'detail' => 'This value should not be null.',
                    ],
                ],
            ]);

        $response = $this->postJson('/rest/users', ['data' => ['attributes' => ['name' => 'testing user4', 'email' => 'test4email@test.com', 'password' => '123456']]]);
        $response
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 4,
                    'attributes' => [
                        'name' => 'testing user4',
                        'email' => 'test4email@test.com'
                    ]
                ]
            ]);

        $this->get('/rest/users/4')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 4,
                    'attributes' => [
                        'name' => 'testing user4',
                        'email' => 'test4email@test.com'
                    ]
                ]
            ]);
    }

    public function test_show_action()
    {
        $this->get('/rest/users/1')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'attributes' => [
                        'name' => 'testing user1',
                        'email' => 'test1email@test.com',
                    ]
                ],
            ]);

        $this->get('/rest/users/777')->assertStatus(404);
    }

    public function test_delete_action()
    {
        $this->delete('/rest/users/2')->assertSuccessful();
        $this->delete('/rest/users/2')->assertStatus(404);
        $this->get('/rest/users/2')->assertStatus(404);
        $response = $this->get('/rest/users');
        $response
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 1], ['id' => 3]],
            ]);

    }

    public function test_index_action()
    {
        $this->get('/rest/users')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 1], ['id' => 2], ['id' => 3]],
            ]);

        $response = $this->get('/rest/users?page[limit]=1&page[offset]=2');
        $response
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 3]],
                'meta' => [
                    'pagination' => [
                        'total' => 3,
                        'count' => 1,
                        'per_page' => 1,
                        'current_page' => 3,
                        'total_pages' => 3,
                    ],
                ],
                'links' => [],
            ]);

        $this->get('/rest/users?page[limit]=2&page[offset]=2')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    [
                        'id' => 3,
                        'attributes' => [
                            'name' => 'testing user3',
                            'email' => 'test3email@test.com',
                        ]
                    ]
                ],
                'meta' => [
                    'pagination' => [
                        'total' => 3,
                        'count' => 1,
                        'per_page' => 2,
                        'current_page' => 2,
                        'total_pages' => 2,
                    ],
                ],
                'links' => [
                ]
            ]);

        $this->get('/rest/users?sort=-id')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 3], ['id' => 2], ['id' => 1]],
            ]);

        $this->get('/rest/users?filter=@test.com')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 1], ['id' => 3]],
            ]);

        $this->get('/rest/users?filter=@test.com&page[limit]=1')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 1]],
                'meta' => [
                    'pagination' => [
                        'total' => 2,
                        'count' => 1,
                        'per_page' => 1,
                        'current_page' => 1,
                        'total_pages' => 2,
                    ],
                ],
                'links' => [],
            ]);

        $response = $this->get('/rest/users?filter=@test.com&page[number]=2&page[size]=1');
        $response
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 3]],
                'meta' => [
                    'pagination' => [
                        'total' => 2,
                        'count' => 1,
                        'per_page' => 1,
                        'current_page' => 2,
                        'total_pages' => 2,
                    ],
                ],
                'links' => [],
            ]);

        $response = $this->get('/rest/users?page[limit]=1&sort=-id&filter[id][start]=1&filter[id][end]=2');
        $response
            ->assertHeader('Content-Type', 'application/vnd.api+json')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 2]],
                'meta' => [
                    'pagination' => [
                        'total' => 2,
                        'count' => 1,
                        'per_page' => 1,
                        'current_page' => 1,
                        'total_pages' => 2,
                    ],
                ],
                'links' => [],
            ]);
    }

    public function test_handle_authorization_exception()
    {
        /** @var User $user */
        $user = $this->em->find(User::class, 2);

        $data = ['attributes' => ['name' => 'testing user4', 'email' => 'test4email@test.com', 'password' => '123456']];

        $this->actingAs($user);
        $this->getJson('/rest/users')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->getJson('/rest/users/1')->assertStatus(Response::HTTP_FORBIDDEN);
        $this->postJson('/rest/users', ['data' => $data])->assertStatus(Response::HTTP_FORBIDDEN);
        $this->patchJson('/rest/users/2', ['data' => $data])->assertStatus(Response::HTTP_FORBIDDEN);
        $this->deleteJson('/rest/users/2')->assertStatus(Response::HTTP_FORBIDDEN);

    }
}
