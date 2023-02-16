<?php

namespace Tests\Action\Show;

use Illuminate\Support\Facades\Route;
use Pz\LaravelDoctrine\JsonApi\Action\Show\ShowResource;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Pz\LaravelDoctrine\JsonApi\ResourceRepository;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
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
                    ResourceRepository::create($this->em, User::class),
                    new UserTransformer()
                )
            )
                ->dispatch($request);
        });

        Route::get('/roles/{id}', function (JsonApiRequest $request) {
            return (
                new ShowResource(
                    ResourceRepository::create($this->em, Role::class),
                    new RoleTransformer()
                )
            )
                ->dispatch($request);
        });
    }

    public function testAuthenticationPermissions()
    {
        $this->get('/users/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/users/3')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->get('/roles/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/roles/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->actingAsUser();
        $this->get('/users/1')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/users/3')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->get('/roles/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->get('/roles/2')->assertStatus(JsonApiResponse::HTTP_OK);

        $this->actingAsRoot();
        $this->get('/users/1')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/2')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/users/3')->assertStatus(JsonApiResponse::HTTP_OK);

        $this->get('/roles/1')->assertStatus(JsonApiResponse::HTTP_OK);
        $this->get('/roles/2')->assertStatus(JsonApiResponse::HTTP_OK);
    }

    public function testResponseData()
    {
        $user = $this->actingAsUser();
        $this->get('/users/'.$user->getId())
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
    }
}
