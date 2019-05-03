<?php

namespace Tests\Feature\Controllers;

use App\User;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServerControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function notAdminCanNotAddNewServer()
    {
        $this->json('POST', '/api/servers', [])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $nonAdmin = factory(User::class)->create(['role' => null]);
        $this->actingAs($nonAdmin, 'api')
            ->json('POST', '/api/servers', [])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function createSavesToToDatabase()
    {
        $testData = [
            'ip'   => '127.0.0.1',
            'name' => 'server',
        ];
        $user = factory(User::class)->state('admin')->create();
        $this->actingAs($user, 'api')
            ->json('POST', '/api/servers', $testData)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJson($testData);

        $this->assertDatabaseHas('servers', $testData);
    }

    /** @test */
    public function createValidatesData()
    {
        $user = factory(User::class)->state('admin')->create();
        $this->actingAs($user, 'api')
            ->json('POST', '/api/servers', ['ip' => '127.0.0.1'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $this->actingAs($user, 'api')
            ->json('POST', '/api/servers', ['name' => 'server'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
