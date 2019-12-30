<?php

namespace Tests\Unit;

use App\Checker;
use App\CheckerLog;
use App\Checkers\Exceptions\Alert;
use App\Checkers\Exceptions\CheckerException;
use App\Checkers\Exceptions\Emergency;
use App\Site;
use Carbon\Carbon;
use Tests\Stubs\Checkers\AlertStub;
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

    /** @test */
    public function whenCheckPassDontWriteLog()
    {
        $this->app->instance('TestChecker', new class implements \App\Checkers\Checker {
            public function check($model, array $arguments)
            {
            }

            public function nextRun(): ?Carbon
            {
                return null;
            }
        });

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker']);
        $checker->check();

        $this->assertEquals(0, CheckerLog::count());
    }

    /** @test */
    public function logAlertWhenCheckerExceptionIsThrown()
    {
        $this->app->instance('TestChecker', new AlertStub());

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker']);
        $checker->check();

        $this->assertEquals(1, CheckerLog::count());
        $this->assertDatabaseHas('checker_logs', [
            'checker_id' => $checker->id,
            'message'    => 'Some Error',
            'level'      => CheckerException::ALERT,
        ]);
    }

    /** @test */
    public function dontLogSameAlertTwice()
    {
        $this->app->instance('TestChecker', new AlertStub());

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker']);
        $checker->check();
        $checker->check();

        $this->assertEquals(1, CheckerLog::count());
        $this->assertDatabaseHas('checker_logs', [
            'checker_id' => $checker->id,
            'message'    => 'Some Error',
            'level'      => CheckerException::ALERT,
        ]);
    }

    /** @test */
    public function logDifferentError()
    {
        $this->app->instance('TestChecker', new class implements \App\Checkers\Checker {
            public static int $counter = 0;

            public function check($model, array $arguments)
            {
                self::$counter++;
                if (self::$counter == 1) {
                    throw Alert::create('Test alert');
                }

                throw Emergency::create('Test emergency');
            }

            public function nextRun(): ?Carbon
            {
                return null;
            }
        });

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker']);
        $checker->check();
        $checker->check();
        $checker->check();
        $checker->check();

        $this->assertEquals(2, CheckerLog::count());
    }
}
