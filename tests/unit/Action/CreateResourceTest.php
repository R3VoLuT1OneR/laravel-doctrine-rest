<?php

namespace Tests\Action;

use Illuminate\Support\Facades\Route;
use Tests\App\Actions\User\CreateUserAction;
use Tests\App\Actions\User\CreateUserRequest;
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
        $body = [
            'data' => [
                'attributes' => [
                    'name' => 'New user',
                    'email' => 'newuser@gmail.com',
                    'password' => 'secret',
                ]
            ]
        ];

        $response = $this->post('/users', $body);
        $response->assertCreated();
    }
}