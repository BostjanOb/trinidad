<?php

namespace Tests\Feature\Controllers;

use App\Server;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServersControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function indexReturnsCollection()
    {
        factory(Server::class, 5)->create();

        $this->asUser()
            ->json('GET', '/api/servers')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['total' => 5])
            ->assertJsonStructure(['data' => ['*' => ['id', 'name', 'ip']]]);
    }

    /** @test */
    public function indexPerPageReturnCorrectCollection()
    {
        factory(Server::class, 150)->create();

        $this->asUser()
            ->json('GET', '/api/servers')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['total' => 150, 'per_page' => 25, 'last_page' => 6]);

        $this->asUser()
            ->json('GET', '/api/servers?per_page=50')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['total' => 150, 'per_page' => 50, 'last_page' => 3]);
    }

    /** @test */
    public function createNotAdminCanNotAddNewServer()
    {
        $this->json('POST', '/api/servers', [])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);

        $this->asUser()
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
        $this->asAdmin()
            ->json('POST', '/api/servers', $testData)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment($testData);

        $this->assertDatabaseHas('servers', $testData);
    }

    /** @test */
    public function createWithoutNameSetsIpAsName()
    {
        $this->asAdmin()
            ->json('POST', '/api/servers', ['ip'   => '127.0.0.1'])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonFragment(['ip'   => '127.0.0.1', 'name' => '127.0.0.1']);

        $this->assertDatabaseHas('servers', ['ip'   => '127.0.0.1', 'name' => '127.0.0.1']);
    }

    /** @test */
    public function createValidatesData()
    {
        $this->asAdmin()
            ->json('POST', '/api/servers', ['name' => 'server'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function showReturn404ForNotFound()
    {
        $this->asUser()
            ->json('GET', '/api/servers/1')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function showReturnsCorrectJson()
    {
        $server = factory(Server::class)->create();
        $this->asUser()
            ->json('GET', '/api/servers/' . $server->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    'id'   => $server->id,
                    'name' => $server->name,
                    'ip'   => $server->ip,
                ],
            ]);
    }

    /** @test */
    public function updateCanOnlyBeMadeByAdmin()
    {
        $server = factory(Server::class)->create();
        $this->asUser()
            ->json('PUT', '/api/servers/' . $server->id, ['name' => 'server'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function updateUpdatesFields()
    {
        $this->withExceptionHandling();
        $server = factory(Server::class)->create(['name' => 'foo']);
        $this->asAdmin()
            ->json('PUT', '/api/servers/' . $server->id, ['name' => 'bar'])
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    'id'   => $server->id,
                    'name' => 'bar',
                    'ip'   => $server->ip,
                ],
            ]);

        $this->assertDatabaseHas('servers', [
            'id'   => $server->id,
            'name' => 'bar',
            'ip'   => $server->ip,
        ]);
    }

    /** @test */
    public function deleteCanOnlyBeMadeByAdmin()
    {
        $server = factory(Server::class)->create();
        $this->asUser()
            ->json('DELETE', '/api/servers/' . $server->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function deleteDeletesTheServer()
    {
        $server = factory(Server::class)->create();
        $this->asAdmin()
            ->json('DELETE', '/api/servers/' . $server->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('servers', ['id' => $server->id]);
    }
}
