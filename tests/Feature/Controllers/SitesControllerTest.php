<?php

namespace Tests\Feature\Controllers;

use App\Server;
use App\Site;
use App\UrlResolver;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SitesControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function indexListAllSites()
    {
        factory(Site::class, 10)->create();

        $this->asUser()
            ->json('GET', '/api/sites')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['total' => 10])
            ->assertJsonStructure(['data' => ['*' => ['id', 'name', 'domain']]]);
    }

    /** @test */
    public function indexPagination()
    {
        factory(Site::class, 100)->create();

        $this->asUser()
            ->json('GET', '/api/sites?per_page=10&page=2')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['total' => 100, 'per_page' => 10, 'last_page' => 10, 'current_page' => 2])
            ->assertJsonStructure(['data' => ['*' => ['id', 'name', 'domain']]]);
    }

    /** @test */
    public function indexGroupServersAsMetadata()
    {
        $servers = factory(Server::class, 2)->create();
        foreach ($servers as $server) {
            factory(Site::class, 10)->create(['server_id' => $server->id]);
        }

        $this->asUser()
            ->json('GET', '/api/sites?per_page=40')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['total' => 20])
            ->assertJsonStructure(['servers' => ['*' => ['id', 'name', 'ip']]]);
    }

    /** @test */
    public function indexShowOnlySitesFromSpecificServer()
    {
        $servers = factory(Server::class, 2)->create();
        foreach ($servers as $server) {
            factory(Site::class, 10)->create(['server_id' => $server->id]);
        }

        $response = $this->asUser()
            ->json('GET', '/api/sites?per_page=40&server=' . $servers[0]->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['total' => 10])
            ->decodeResponseJson();

        foreach ($response['data'] as $site) {
            $this->assertEquals($servers[0]->id, $site['server_id']);
        }
    }

    /** @test */
    public function showReturns404ForInvalidSite()
    {
        $this->asUser()
            ->json('GET', '/api/sites/4')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function showReturnsSite()
    {
        $site = factory(Site::class)->create();

        $this->asUser()
            ->json('GET', '/api/sites/' . $site->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['id' => $site->id]);
    }

    /** @test */
    public function createUserCannotCreateNewSite()
    {
        $this->asUser()
            ->json('POST', '/api/sites', ['url' => 'http://foo.bar'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function createAddsNewSiteAndServer()
    {
        $this->mock(UrlResolver::class, function ($mock) {
            $mock->shouldReceive('domain')->once()->passthru();
            $mock->shouldReceive('ip')->once()->with('http://foo.bar')->andReturn('1.2.3.4');
        });

        $this->asAdmin()
            ->json('POST', '/api/sites', ['url' => 'http://foo.bar'])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['data' => ['id', 'domain', 'name']])
            ->assertJsonFragment(['domain' => 'foo.bar', 'name' => 'foo.bar'])
            ->assertJsonFragment(['ip' => '1.2.3.4']);

        $this->assertDatabaseHas('servers', ['id' => 1, 'ip' => '1.2.3.4']);
        $this->assertDatabaseHas('sites', ['id' => 1, 'server_id' => 1, 'domain' => 'foo.bar']);
    }

    /** @test */
    public function createAddsNewSiteAndLinkToExistingServer()
    {
        $this->mock(UrlResolver::class, function ($mock) {
            $mock->shouldReceive('domain')->once()->passthru();
            $mock->shouldReceive('ip')->once()->with('http://foo.bar')->andReturn('1.2.3.4');
        });

        $server = factory(Server::class)->create(['ip' => '1.2.3.4']);

        $this->asAdmin()
            ->json('POST', '/api/sites', ['url' => 'http://foo.bar'])
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas('sites', ['server_id' => $server->id, 'domain' => 'foo.bar']);
    }

    /** @test */
    public function updateOnlyAdminCanUpdateSite()
    {
        $site = factory(Site::class)->create(['name' => 'foo']);

        $this->asUser()
            ->json('PUT', '/api/sites/' . $site->id, ['name' => 'bar'])
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function updateUpdatesName()
    {
        $site = factory(Site::class)->create(['name' => 'foo']);

        $this->asAdmin()
            ->json('PUT', '/api/sites/' . $site->id, ['name' => 'bar'])
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('sites', ['id' => $site->id, 'name' => 'bar']);
    }

    /** @test */
    public function updatesDontUpdateDomain()
    {
        $site = factory(Site::class)->create(['domain' => 'foo.bar']);

        $this->asAdmin()
            ->json('PUT', '/api/sites/' . $site->id, ['domain' => 'john.doe'])
            ->assertStatus(Response::HTTP_OK);

        $this->assertDatabaseHas('sites', ['id' => $site->id, 'domain' => $site->domain]);
    }

    /** @test */
    public function deleteUserCantDeleteSite()
    {
        $site = factory(Site::class)->create();

        $this->asUser()
            ->json('DELETE', '/api/sites/' . $site->id)
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function deleteAdminCanDeleteSite()
    {
        $site = factory(Site::class)->create();

        $this->asAdmin()
            ->json('DELETE', '/api/sites/' . $site->id)
            ->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('sites', ['id' => $site->id]);
    }
}
