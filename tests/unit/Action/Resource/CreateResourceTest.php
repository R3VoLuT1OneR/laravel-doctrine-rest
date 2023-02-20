<?php

namespace Tests\Action\Resource;

use Illuminate\Support\Facades\Route;
use Tests\App\Actions\User\CreateUserAction;
use Tests\App\Actions\User\CreateUserRequest;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\App\Transformers\UserTransformer;
use Tests\TestCase;

class CreateResourceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::post('/users', function (CreateUserRequest $request) {
            return (new CreateUserAction(
                $this->usersRepo(),
                new UserTransformer()
            ))
                ->dispatch($request);
        });
    }

    public function testCreateNewUser()
    {
        $response = $this->post('/users', [
            'data' => [
                'attributes' => [
                    'name' => 'New user',
                    'email' => 'newuser@gmail.com',
                    'password' => 'secret',
                ]
            ]
        ]);

        $response->assertCreated();

        $this->em()->clear();
        $newUser = $this->em()->find(User::class, $response->json('data.id'));

        $this->assertTrue($newUser->getRoles()->contains(Role::user()));
    }

    public function testCantCreateUserWithRootRole()
    {
        $response = $this->post('/users', [
            'data' => [
                'attributes' => [
                    'name' => 'New user',
                    'email' => 'newuser@gmail.com',
                    'password' => 'secret',
                ],
                'relationships' => [
                    'roles' => [
                        'data' => [
                            ['type' => 'roles', 'id' => (string) Role::root()->getId()],
                        ]
                    ]
                ]
            ]
        ]);

        $response->assertCreated();

        $this->em()->clear();
        $newUser = $this->em()->find(User::class, $response->json('data.id'));

        $this->assertTrue($newUser->getRoles()->contains(Role::user()));
        $this->assertFalse($newUser->getRoles()->contains(Role::root()));
    }

    public function testUserCreateValidation(): void
    {
        $response = $this->post('/users');
        $response->assertExactJson([
            'errors' => [
                [
                    'code' => '422',
                    'detail' => 'validation.required',
                    'source' => [
                        'pointer' => '/data/attributes/name'
                    ],
                ],
                [
                    'code' => '422',
                    'detail' => 'validation.required',
                    'source' => [
                        'pointer' => '/data/attributes/password'
                    ],
                ],
                [
                    'code' => '422',
                    'detail' => 'validation.required',
                    'source' => [
                        'pointer' => '/data/attributes/email'
                    ],
                ],
            ]
        ]);

        $response = $this->post('/users', [
            'data' => [
                'attributes' => [
                    'name' => 'New user',
                    'password' => 'secret',
                    'email' => 'not email',
                ]
            ]
        ]);

        $response->assertExactJson([
            'errors' => [
                [
                    'code' => '422',
                    'detail' => 'validation.email',
                    'source' => [
                        'pointer' => '/data/attributes/email'
                    ],
                ]
            ]
        ]);
    }
}
