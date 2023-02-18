<?php

namespace Tests\Action;

use Illuminate\Support\Facades\Route;
use Pz\LaravelDoctrine\JsonApi\Action\RemoveResource;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Tests\App\Entities\User;
use Tests\App\Transformers\UserTransformer;
use Tests\TestCase;

class RemoveResourceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::delete('/users/{id}', function (JsonApiRequest $request) {
            return (new RemoveResource(
                $this->usersRepo(),
                new UserTransformer()
            ))
                ->dispatch($request);
        });
    }

    public function testAuthorizationPermissionsForNoLoggedIn()
    {
        $this->delete('/users/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->em()->clear();
        $this->assertNotNull($this->em()->find(User::class, 1));
        $this->assertNotNull($this->em()->find(User::class, 2));
        $this->assertNotNull($this->em()->find(User::class, 3));
    }

    public function testAuthorizationPermissionsForUserRole()
    {
        $this->actingAsUser();

        $this->delete('/users/1')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->em()->clear();
        $this->assertNull($this->em()->find(User::class, 1));
        $this->assertNotNull($this->em()->find(User::class, 2));
        $this->assertNotNull($this->em()->find(User::class, 3));
    }

    public function testAuthorizationPermissionsForModeratorRole()
    {
        $this->actingAsModerator();

        $this->delete('/users/1')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);
        $this->delete('/users/3')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);

        $this->em()->clear();
        $this->assertNotNull($this->em()->find(User::class, 1));
        $this->assertNotNull($this->em()->find(User::class, 2));
        $this->assertNull($this->em()->find(User::class, 3));
    }

    public function testAuthorizationPermissionsForRootRole()
    {
        $this->actingAsRoot();

        $this->delete('/users/1')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);
        $this->delete('/users/2')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);
        $this->delete('/users/3')->assertStatus(JsonApiResponse::HTTP_NO_CONTENT);

        $this->em()->clear();
        $this->assertNull($this->em()->find(User::class, 1));
        $this->assertNull($this->em()->find(User::class, 2));
        $this->assertNull($this->em()->find(User::class, 3));
    }
}