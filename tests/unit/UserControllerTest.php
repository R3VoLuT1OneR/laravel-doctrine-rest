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
        $response = $this->patchJson('/rest/users/1/relationships/roles', ['data' => [
            ['id' => Role::USER, 'type' => Role::getResourceKey()],
        ]]);
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
}
