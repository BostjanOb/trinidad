<?php

namespace Tests\Unit\Checkers\Site;

use App\Checkers\Site\HostingServer;
use App\Server;
use App\Site;
use App\UrlResolver;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DomainsControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dontThrowExceptionForSameIp()
    {
        $server = factory(Server::class)->create(['ip' => '127.0.0.1']);
        $site = factory(Site::class)->create(['server_id' => $server->id]);

        $this->mock(UrlResolver::class, function ($mock) use ($server, $site) {
            $mock->shouldReceive('ip')->once()->with($site->host)->andReturn($server->ip);
        });

        app(HostingServer::class)->check($site);

        $this->assertDatabaseHas('sites', ['id' => $site->id, 'server_id' => $server->id]);
    }

    /** @test */
    public function throwAlertAndAssignNewServer()
    {
        $server = factory(Server::class)->create(['ip' => '127.0.0.1']);
        $server1 = factory(Server::class)->create(['ip' => '127.0.0.2']);

        $site = factory(Site::class)->create(['server_id' => $server->id]);

        $this->mock(UrlResolver::class, function ($mock) use ($server1, $site) {
            $mock->shouldReceive('ip')->once()->with($site->host)->andReturn($server1->ip);
        });

        $this->expectException(\Exception::class);

        app(HostingServer::class)->check($site);

        $this->assertDatabaseHas('sites', ['id' => $site->id, 'server_id' => $server1->id]);
    }

    /** @test */
    public function nullForNextCheck()
    {
        $this->assertNull(app(HostingServer::class)->nextRun());
    }
}
