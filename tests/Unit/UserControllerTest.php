<?php namespace Pz\LaravelDoctrine\Rest\Tests\Unit;

use Doctrine\ORM\EntityManager;
use Illuminate\Support\Facades\Route;

use Pz\LaravelDoctrine\Rest\Tests\App\Rest\UserController;
use Pz\LaravelDoctrine\Rest\Tests\TestCase;
use Pz\LaravelDoctrine\Rest\Tests\App\Entities\User;

class UserControllerTest extends TestCase
{

    /**
     * @var User
     */
    protected $user;

    public function setUp()
    {
        parent::setUp();
        Route::get('/rest/users', UserController::class.'@index');
        Route::get('/rest/users/{id}', UserController::class.'@show');
        Route::post('/rest/users', UserController::class.'@store');
        Route::put('/rest/users/{id}', UserController::class.'@edit');
        Route::delete('/rest/users/{id}', UserController::class.'@delete');

        $this->user = $this->em->find(User::class, 1);
        $this->actingAs($this->user);
    }

    public function test_edit_action()
    {
        $this->putJson('/rest/users/3', ['email' => 'fasdfas'])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['validation.email'],
                ],
            ]);

        $this->putJson('/rest/users/3', ['email' => 'testEdit@edit.com'])
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 3,
                    'name' => 'testing user3',
                    'email' => 'testEdit@edit.com',
                ],
            ]);

        $this->get('/rest/users/3')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 3,
                    'name' => 'testing user3',
                    'email' => 'testEdit@edit.com',
                ],
            ]);
    }

    public function test_store_action()
    {
        $this->postJson('/rest/users')
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'name' => ['validation.required'],
                    'email' => ['validation.required'],
                    'password' => ['validation.required'],
                ],
            ]);

        $this->postJson('/rest/users', ['name' => 'test1', 'email' => 'fasdf', 'password' => 'aaa'])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['validation.email'],
                    'password' => ['validation.min.string'],
                ],
            ]);

        $this->postJson('/rest/users', ['name' => 'testing user4', 'email' => 'test4email@test.com', 'password' => '123456'])
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 4,
                    'name' => 'testing user4',
                    'email' => 'test4email@test.com'
                ]
            ]);

        $this->get('/rest/users/4')
            ->assertSuccessful()
            ->assertJson([
                'data' => [
                    'id' => 4,
                    'name' => 'testing user4',
                    'email' => 'test4email@test.com'
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
                    'name' => 'testing user1',
                    'email' => 'test1email@test.com',
                ],
            ]);

        $this->get('/rest/users/777')->assertStatus(404);
    }

    public function test_delete_action()
    {
        $this->delete('/rest/users/2')->assertSuccessful();
        $this->delete('/rest/users/2')->assertStatus(404);
        $this->get('/rest/users/2')->assertStatus(404);
        $this->get('/rest/users')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 1], ['id' => 3]],
                'meta' => [
                    'count' => 2,
                    'limit' => 10,
                    'start' => 0,
                ]
            ]);

    }

    public function test_index_action()
    {
        $this->get('/rest/users')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 1], ['id' => 2], ['id' => 3]],
                'meta' => [
                    'count' => 3,
                    'limit' => 10,
                    'start' => 0,
                ]
            ]);

        $this->get('/rest/users?limit=1&start=2')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 3]],
                'meta' => [
                    'count' => 3,
                    'limit' => 1,
                    'start' => 2,
                ]
            ]);

        $this->get('/rest/users?limit=2&page=2')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 3]],
                'meta' => [
                    'count' => 3,
                    'limit' => 2,
                    'start' => 2
                ]
            ]);

        $this->get('/rest/users?orderBy=id&ascending=0')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 3], ['id' => 2], ['id' => 1]],
                'meta' => [
                    'count' => 3,
                    'limit' => 10,
                    'start' => 0,
                ]
            ]);

        $this->get('/rest/users?query=@test.com')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 1], ['id' => 3]],
                'meta' => [
                    'count' => 2,
                    'limit' => 10,
                    'start' => 0,
                ]
            ]);

        $this->get('/rest/users?query=@test.com&limit=1&page=1')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 1]],
                'meta' => [
                    'count' => 2,
                    'limit' => 1,
                    'start' => 0,
                ]
            ]);

        $this->get('/rest/users?query=@test.com&limit=1&page=2')
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 3]],
                'meta' => [
                    'count' => 2,
                    'limit' => 1,
                    'start' => 1,
                ]
            ]);

        $this->get('/rest/users?limit=1&orderBy=id&ascending=0&query=' . json_encode(['id' => ['start' => 1, 'end' => 3]]))
            ->assertSuccessful()
            ->assertJson([
                'data' => [['id' => 2]],
                'meta' => [
                    'count' => 2,
                    'limit' => 1,
                    'start' => 0,
                ]
            ]);
    }
}
