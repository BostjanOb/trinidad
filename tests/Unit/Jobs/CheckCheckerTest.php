<?php

namespace Tests\Unit\Jobs;

use App\Checker;
use App\CheckerLog;
use App\Checkers\Exceptions\Alert;
use App\Checkers\Exceptions\CheckerException;
use App\Checkers\Exceptions\Emergency;
use App\Jobs\CheckChecker;
use Carbon\Carbon;
use Event;
use Tests\Stubs\Checkers\AlertStub;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckCheckerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function whenCheckPassDontWriteLog()
    {
        Event::fake();

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

        (new CheckChecker($checker))->handle();

        $this->assertEquals(0, CheckerLog::count());

        Event::assertNotDispatched(\App\Events\CheckerException::class);
    }

    /** @test */
    public function logAlertWhenCheckerExceptionIsThrown()
    {
        Event::fake();

        $this->app->instance('TestChecker', new AlertStub());

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker']);

        (new CheckChecker($checker))->handle();

        $this->assertEquals(1, CheckerLog::count());
        $this->assertDatabaseHas('checker_logs', [
            'checker_id' => $checker->id,
            'message'    => 'Some Error',
            'level'      => CheckerException::ALERT,
        ]);

        Event::assertDispatched(\App\Events\CheckerException::class);
    }

    /** @test */
    public function dontLogSameAlertTwice()
    {
        Event::fake();

        $this->app->instance('TestChecker', new AlertStub());

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker']);

        $job = new CheckChecker($checker);
        $job->handle();
        $job->handle();

        $this->assertEquals(1, CheckerLog::count());
        $this->assertDatabaseHas('checker_logs', [
            'checker_id' => $checker->id,
            'message'    => 'Some Error',
            'level'      => CheckerException::ALERT,
        ]);

        Event::assertDispatched(\App\Events\CheckerException::class, 1);
    }

    /** @test */
    public function logDifferentError()
    {
        Event::fake();

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
        $job = new CheckChecker($checker);
        $job->handle();
        $job->handle();
        $job->handle();
        $job->handle();

        $this->assertEquals(2, CheckerLog::count());

        Event::assertDispatched(\App\Events\CheckerException::class, 2);
    }

    /** @test */
    public function markResolvedSingleFailLog()
    {
        Event::fake();

        $testDate = now();
        Carbon::setTestNow($testDate);
        $this->app->instance('TestChecker', new class implements \App\Checkers\Checker {
            public static int $counter = 0;

            public function check($model, array $arguments)
            {
                self::$counter++;
                if (self::$counter == 1) {
                    throw Alert::create('Test alert');
                }
            }

            public function nextRun(): ?Carbon
            {
                return null;
            }
        });

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker']);
        $job = new CheckChecker($checker);
        $job->handle();
        $job->handle();
        $job->handle();

        $this->assertEquals(1, CheckerLog::count());

        $this->assertDatabaseHas('checker_logs', [
            'checker_id'  => $checker->id,
            'level'       => CheckerException::ALERT,
            'resolved_at' => $testDate,
        ]);

        Event::assertDispatched(\App\Events\CheckerException::class);
        Event::assertDispatched(\App\Events\CheckerResolved::class);
    }

    /** @test */
    public function markResolvedManyLogs()
    {
        Event::fake();

        $testDate = now();
        Carbon::setTestNow($testDate);
        $this->app->instance('TestChecker', new class implements \App\Checkers\Checker {
            public static int $counter = 0;

            public function check($model, array $arguments)
            {
                self::$counter++;
                if (self::$counter == 1) {
                    throw Alert::create('Test alert');
                }
                if (self::$counter == 2) {
                    throw Emergency::create('Test emergency');
                }
            }

            public function nextRun(): ?Carbon
            {
                return null;
            }
        });

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker']);
        $job = new CheckChecker($checker);
        $job->handle();
        $job->handle();
        $job->handle();
        $job->handle();
        $job->handle();

        $this->assertEquals(2, CheckerLog::count());

        $this->assertDatabaseHas('checker_logs', [
            'checker_id'  => $checker->id,
            'level'       => CheckerException::ALERT,
            'resolved_at' => $testDate,
        ]);

        $this->assertDatabaseHas('checker_logs', [
            'checker_id'  => $checker->id,
            'level'       => CheckerException::EMERGENCY,
            'resolved_at' => $testDate,
        ]);

        Event::assertDispatched(\App\Events\CheckerException::class, 2);
        Event::assertDispatched(\App\Events\CheckerResolved::class, 2);
    }

    /** @test */
    public function setCorrectNextRunWhenReturnIsNull()
    {
        Event::fake();

        $testDate = now();
        Carbon::setTestNow($testDate);
        $this->app->instance('TestChecker', new class implements \App\Checkers\Checker {
            public function check($model, array $arguments)
            {
            }

            public function nextRun(): ?Carbon
            {
                return null;
            }
        });

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker', 'interval' => 15]);

        (new CheckChecker($checker))->handle();

        $this->assertDatabaseHas('checkers', [
            'id'       => $checker->id,
            'next_run' => $testDate->addMinutes(15),
        ]);
    }

    /** @test */
    public function setCorrectNextRunWhenDateIsReturned()
    {
        Event::fake();

        $testDate = now();
        Carbon::setTestNow($testDate);

        $this->app->instance('TestChecker', new class implements \App\Checkers\Checker {
            public function check($model, array $arguments)
            {
            }

            public function nextRun(): ?Carbon
            {
                return now()->addDays(10)->addMinutes(10);
            }
        });

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker', 'interval' => 15]);

        (new CheckChecker($checker))->handle();

        $this->assertDatabaseHas('checkers', [
            'id'       => $checker->id,
            'next_run' => now()->addDays(10)->addMinutes(10),
        ]);
    }

    /** @test */
    public function setCorrectNextRunWhenErrorIsReturned()
    {
        Event::fake();

        $testDate = now();
        Carbon::setTestNow($testDate);

        $this->app->instance('TestChecker', new class implements \App\Checkers\Checker {
            public function check($model, array $arguments)
            {
                throw Alert::create('Test alert');
            }

            public function nextRun(): ?Carbon
            {
                return now()->addDays(10)->addMinutes(10);
            }
        });

        $checker = factory(Checker::class)->create(['checker' => 'TestChecker', 'interval' => 15]);

        (new CheckChecker($checker))->handle();

        $this->assertDatabaseHas('checkers', [
            'id'       => $checker->id,
            'next_run' => now()->addDays(10)->addMinutes(10),
        ]);
    }
}
