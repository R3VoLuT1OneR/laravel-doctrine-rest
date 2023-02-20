<?php

namespace Tests\Action\Resource;

use Illuminate\Support\Facades\Route;
use Pz\LaravelDoctrine\JsonApi\Action\Resource\ShowResource;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Tests\App\Actions\Page\ShowPageResource;
use Tests\App\Actions\Page\ShowRelatedComments;
use Tests\App\Entities\Role;
use Tests\App\Transformers\PageCommentTransformer;
use Tests\App\Transformers\PagesTransformer;
use Tests\App\Transformers\RoleTransformer;
use Tests\App\Transformers\UserTransformer;
use Tests\TestCase;

class ShowResourceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::get('/users/{id}', function (JsonApiRequest $request) {
            return (
                new ShowResource(
                    $this->usersRepo(),
                    new UserTransformer()
                )
            )
                ->dispatch($request);
        });

        Route::get('/roles/{id}', function (JsonApiRequest $request) {
            return (
                new ShowResource(
                    $this->rolesRepo(),
                    new RoleTransformer()
                )
            )
                ->dispatch($request);
        });

        Route::get('/pages/{id}', function (JsonApiRequest $request) {
            return (
                new ShowPageResource(
                    $this->pageRepo(),
                    new PagesTransformer()
                )
            )
                ->dispatch($request);
        });

        Route::get('/pageComments/{id}', function (JsonApiRequest $request) {
            return (
                new ShowRelatedComments(
                    $this->pageCommentsRepo(),
                    new PageCommentTransformer()
                )
            )
                ->dispatch($request);
        });
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->get('/users/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/users/3')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->get('/roles/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/roles/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->get('/pages/1')->assertStatus(JsonApiResponse::HTTP_OK);

        $this->get('/pageComments/1')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/pageComments/2')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/pageComments/3')->assertStatus(JsonApiResponse::HTTP_OK);
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();
        $this->get('/users/1')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/2')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/3')->assertStatus(JsonApiResponse::HTTP_OK);

        $this->get('/roles/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/roles/2')->assertStatus(JsonApiResponse::HTTP_OK);

        $this->get('/pages/1')->assertStatus(JsonApiResponse::HTTP_OK);

        $this->get('/pageComments/1')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/pageComments/2')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/pageComments/3')->assertStatus(JsonApiResponse::HTTP_OK);
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();
        $this->get('/users/1')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/2')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/3')->assertStatus(JsonApiResponse::HTTP_OK);

        $this->get('/roles/1')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/roles/2')->assertStatus(JsonApiResponse::HTTP_OK);

        $this->get('/pages/1')->assertStatus(JsonApiResponse::HTTP_OK);

        $this->get('/pageComments/1')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/pageComments/2')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/pageComments/3')->assertStatus(JsonApiResponse::HTTP_OK);
    }

    public function testShowUserResponse()
    {
        $this->actingAsRoot();

        $this->get('/users/1')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test1email@test.com',
                        'name' => 'testing user1',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => '/users/1/roles',
                                'self' => '/users/1/relationships/roles'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/users/1'
                    ]
                ]
            ]);

        $this->get('/users/2')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '2',
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test2email@gmail.com',
                        'name' => 'testing user2',
                    ],
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
                ]
            ]);

        $this->get('/users/3')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '3',
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test3email@test.com',
                        'name' => 'testing user3',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => '/users/3/roles',
                                'self' => '/users/3/relationships/roles'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/users/3'
                    ]
                ]
            ]);
    }

    public function testShowRoleResponse()
    {
        $this->actingAsRoot();

        $this->get('/roles/1')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'Root',
                    ],
                    'links' => [
                        'self' => '/roles/1'
                    ]
                ]
            ]);

        $this->actingAsUser();
        $this->get('/roles/2')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '2',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'User',
                    ],
                    'links' => [
                        'self' => '/roles/2'
                    ]
                ]
            ]);

        $this->actingAsModerator();
        $this->get('/roles/3')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '3',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'Moderator',
                    ],
                    'links' => [
                        'self' => '/roles/3'
                    ]
                ]
            ]);
    }

    public function testShowPageResponse()
    {
        $this->get('/pages/1')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'pages',
                    'attributes' => [
                        'title' => 'JSON:API standard',
                        'content' => '<strong>JSON:API</strong>'
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => '/pages/1/user',
                                'self' => '/pages/1/relationships/user'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/pages/1'
                    ]
                ]
            ]);
    }

    public function testShowPageCommentsResponse()
    {
        $this->get('/pageComments/1')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>It is mine comment</span>'
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => '/pageComments/1/user',
                                'self' => '/pageComments/1/relationships/user'
                            ]
                        ],
                        'page' => [
                            'links' => [
                                'related' => '/pageComments/1/page',
                                'self' => '/pageComments/1/relationships/page'
                            ]
                        ],
                    ],
                    'links' => [
                        'self' => '/pageComments/1'
                    ]
                ]
            ]);
        $this->get('/pageComments/2')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '2',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>I know better</span>'
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => '/pageComments/2/user',
                                'self' => '/pageComments/2/relationships/user'
                            ]
                        ],
                        'page' => [
                            'links' => [
                                'related' => '/pageComments/2/page',
                                'self' => '/pageComments/2/relationships/page'
                            ]
                        ],
                    ],
                    'links' => [
                        'self' => '/pageComments/2'
                    ]
                ]
            ]);

        $this->get('/pageComments/3')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '3',
                    'type' => 'pageComments',
                    'attributes' => [
                        'content' => '<span>I think he is right</span>'
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => '/pageComments/3/user',
                                'self' => '/pageComments/3/relationships/user'
                            ]
                        ],
                        'page' => [
                            'links' => [
                                'related' => '/pageComments/3/page',
                                'self' => '/pageComments/3/relationships/page'
                            ]
                        ],
                    ],
                    'links' => [
                        'self' => '/pageComments/3'
                    ]
                ]
            ]);
    }

    public function testIncludeUserRoles()
    {
        $user = $this->actingAsUser();
        $user->addRoles(Role::moderator());
        $this->em()->flush();

        $this->get('/users/1?include=roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'users',
                    'attributes' => [
                        'email' => 'test1email@test.com',
                        'name' => 'testing user1',
                    ],
                    'relationships' => [
                        'roles' => [
                            'data' => [
                                [
                                    'id' => '2',
                                    'type' => 'roles',
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'roles',
                                ],
                            ],
                            'links' => [
                                'related' => '/users/1/roles',
                                'self' => '/users/1/relationships/roles'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/users/1'
                    ]
                ],
                'included' => [
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'User',
                        ],
                        'links' => [
                            'self' => '/roles/2',
                        ]
                    ],
                    [
                        'id' => '3',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'Moderator',
                        ],
                        'links' => [
                            'self' => '/roles/3',
                        ]
                    ],
                ]
            ]);
    }

    public function testIncludePageUserAndUserRoles()
    {
        $this->actingAsModerator();
        $this->get('/pages/1?include=user,user.roles')
            ->assertStatus(403)
            ->assertExactJson([
                'errors' => []
            ]);

        $this->actingAsUser();
        $this->get('/pages/1?include=user,user.roles')
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'id' => '1',
                    'type' => 'pages',
                    'attributes' => [
                        'title' => 'JSON:API standard',
                        'content' => '<strong>JSON:API</strong>'
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => '1',
                                'type' => 'users'
                            ],
                            'links' => [
                                'related' => '/pages/1/user',
                                'self' => '/pages/1/relationships/user'
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => '/pages/1'
                    ]
                ],
                'included' => [
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'User',
                        ],
                        'links' => [
                            'self' => '/roles/2',
                        ]
                   ],
                   [
                       'id' => '1',
                       'type' => 'users',
                       'attributes' => [
                           'email' => 'test1email@test.com',
                           'name' => 'testing user1',
                       ],
                       'relationships' => [
                           'roles' => [
                               'data' => [
                                   [
                                       'id' => '2',
                                       'type' => 'roles',
                                   ],
                               ],
                               'links' => [
                                   'related' => '/users/1/roles',
                                   'self' => '/users/1/relationships/roles'
                               ]
                           ]
                       ],
                       'links' => [
                           'self' => '/users/1'
                       ],
                   ]
                ]
            ]);
    }
}
