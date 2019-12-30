<?php

namespace Tests\Unit;

use App\Checker;
use App\Site;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function getsCorrectChecker()
    {
        $site = factory(Site::class)->create();

        \DB::table('checkers')->insert([
            'id'             => 1,
            'checkable_type' => get_class($site),
            'checkable_id'   => $site->id,
            'checker'        => 'App\\Checkers\\Site\\HostingServer',
            'interval'       => 30,
        ]);

        $checker = Checker::find(1);
        $this->assertInstanceOf('App\\Checkers\\Site\\HostingServer', $checker->checker());
    }

    /** @test */
    public function getsCorrectType()
    {
        $site = factory(Site::class)->create();

        \DB::table('checkers')->insert([
            'id'             => 1,
            'checkable_type' => get_class($site),
            'checkable_id'   => $site->id,
            'checker'        => 'App\\Checkers\\Site\\HostingServer',
            'interval'       => 30,
        ]);

        $checker = Checker::find(1);
        $this->assertInstanceOf('App\\Site', $checker->checkable);
        $this->assertEquals($site->id, $checker->checkable->id);
    }
}
