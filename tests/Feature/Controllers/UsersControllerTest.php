<?php

namespace Tests\Feature\Controllers;

use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function indexUserCantViewAllUsers()
    {
        $this->json('GET', '/api/users')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->asUser()
            ->json('GET', '/api/users')
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function indexAdminCanViewUsers()
    {
        factory(User::class, 10)->create();

        $this->asAdmin()
            ->json('GET', '/api/users')
            ->assertJsonStructure(['data' => ['*' => ['id', 'name', 'email']]])
            ->assertJsonFragment(['total' => 11]);
    }

    /** @test */
    public function createForbiddenForUser()
    {
        $this->json('POST', '/api/users', ['name' => 'john', 'email' => 'foo@bar.com', 'password' => 'bar'])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->asUser()
            ->json('POST', '/api/users', ['name' => 'john', 'email' => 'foo@bar.com', 'password' => 'bar'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function createValidatesData()
    {
        $this->asAdmin();

        $this->json('POST', '/api/users', ['name' => 'john'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->json('POST', '/api/users', ['password' => 'bar'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->json('POST', '/api/users', ['email' => 'bar@foo.com'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $user = factory(User::class)->create();
        $this->asAdmin()
            ->json('POST', '/api/users', ['name' => 'john', 'email' => $user->email, 'password' => 'bar'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function createCreatesNewUser()
    {
        $this->asAdmin()
            ->json('POST', '/api/users', ['name' => 'john', 'email' => 'foo@bar.com', 'password' => 'bar'])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['name' => 'john', 'email' => 'foo@bar.com']);

        $this->assertDatabaseHas('users', ['name' => 'john', 'email' => 'foo@bar.com']);
    }

    /** @test */
    public function showUserCanViewOwnData()
    {
        $user = factory(User::class)->create();

        $this->be($user, 'api')
            ->json('GET', '/api/users/'.$user->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['id' => $user->id, 'email' => $user->email]);

        $this->asUser()
            ->json('GET', '/api/users/'.$user->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function showAdminCanViewOtherUsers()
    {
        $user = factory(User::class)->create();

        $this->asAdmin()
            ->json('GET', '/api/users/'.$user->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['id' => $user->id, 'email' => $user->email]);
    }

    /** @test */
    public function updateUserCanUpdateOwnData()
    {
        $user = factory(User::class)->create();

        $this->be($user, 'api')
            ->json('PUT', 'api/users/'.$user->id, ['name' => 'foo'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['name' => 'foo']);

        $this->asUser()
            ->json('PUT', 'api/users/'.$user->id, ['name' => 'foo'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function updateAdminCanUpdateOtherUsersData()
    {
        $user = factory(User::class)->create();

        $this->asAdmin()
            ->json('PUT', 'api/users/'.$user->id, ['name' => 'foo'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['name' => 'foo']);
    }

    /** @test */
    public function updateFailsForExistingEmail()
    {
        $users = factory(User::class, 2)->create();

        $this->asAdmin()
            ->json('PUT', 'api/users/'.$users[0]->id, ['email' => $users[1]->email])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->asAdmin()
            ->json('PUT', 'api/users/'.$users[0]->id, ['email' => $users[0]->email])
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function updatePasswordIsNotSetWhenUpdatingOtherFields()
    {
        $user = factory(User::class)->create();

        $this->be($user, 'api');

        $this->json('PUT', 'api/users/'.$user->id, ['name' => 'foo'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['name' => 'foo']);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'foo', 'password' => $user->password]);

        $this->json('PUT', 'api/users/'.$user->id, ['password' => 'foo'])
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseMissing('users', ['id' => $user->id, 'name' => 'foo', 'password' => $user->password]);
    }

    /** @test */
    public function deleteOnlyAdminCanDeleteUser()
    {
        $user = factory(User::class)->create();

        $this->asUser()
            ->json('DELETE', '/api/users/'.$user->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);

        $this->asAdmin()
            ->json('DELETE', '/api/users/'.$user->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
