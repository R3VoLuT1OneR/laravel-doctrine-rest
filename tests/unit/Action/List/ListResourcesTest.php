<?php

namespace Tests\Action\List;

use Illuminate\Support\Facades\Route;
use Pz\LaravelDoctrine\JsonApi\Action\List\ListResources;
use Pz\LaravelDoctrine\JsonApi\JsonApiRequest;
use Pz\LaravelDoctrine\JsonApi\JsonApiResponse;
use Tests\App\Transformers\RoleTransformer;
use Tests\TestCase;

class ListResourcesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Route::get('/roles', function (JsonApiRequest $request) {
            return (
                new ListResources(
                    $this->rolesRepo(),
                    new RoleTransformer()
                )
            )
                ->dispatch($request);
        });
    }

    public function testAuthenticationPermissions()
    {
        $this->get('/roles')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->actingAsUser();
        $this->get('/roles')->assertStatus(JsonApiResponse::HTTP_FORBIDDEN);

        $this->actingAsRoot();
        $this->get('/roles')->assertStatus(JsonApiResponse::HTTP_OK);
    }

    public function testListRoleResponse()
    {

    }
}
