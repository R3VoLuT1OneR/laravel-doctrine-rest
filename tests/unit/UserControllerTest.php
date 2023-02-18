<?php namespace Tests;

use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\App\Rest\UserController;

use Illuminate\Support\Facades\Route;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;

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
        $response->assertStatus(422);
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
        $this->delete('/rest/users/4')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);
        $this->delete('/rest/users/5')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);
        $this->get('/rest/users/4')->assertStatus(JsonApiResponse::HTTP_NOT_FOUND);
        $this->get('/rest/users/5')->assertStatus(JsonApiResponse::HTTP_NOT_FOUND);
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
        $response->assertStatus(400)
            ->assertJson([
                'errors' => [
                    [
                        'code' => 'missing-data',
                        'source' => [
                            'pointer' => '/',
                        ],
                        'detail' => 'Data is missing or not an array on pointer level.'
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
}
