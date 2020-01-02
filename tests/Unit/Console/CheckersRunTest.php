<?php

namespace Tests\Unit\Console;

use App\Checker;
use App\Console\Commands\CheckersRun;
use App\Jobs\CheckChecker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckersRunTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dispatchJobForNextRun()
    {
        Bus::fake();

        $testDate = now();
        Carbon::setTestNow($testDate);

        $checker = factory(Checker::class)->create(['next_run' => now()->subHour()]);

        $this->artisan(CheckersRun::class);

        Bus::assertDispatched(CheckChecker::class, fn($job) => $job->checker->id === $checker->id);
    }

    /** @test */
    public function dontDispatchJobForNextRunAfterNow()
    {
        Bus::fake();

        $testDate = now();
        Carbon::setTestNow($testDate);

        $run1 = factory(Checker::class)->create(['next_run' => now()->subHour()]);
        $run2 = factory(Checker::class)->create(['next_run' => now()->subHours(3)]);
        $run3 = factory(Checker::class)->create(['next_run' => now()->subHours(4)]);

        factory(Checker::class)->create(['next_run' => now()->addHours(4)]);
        factory(Checker::class)->create(['next_run' => now()->addHours(4)]);
        factory(Checker::class)->create(['next_run' => now()->addHours(4)]);

        $this->artisan(CheckersRun::class);

        Bus::assertDispatchedTimes(CheckChecker::class, 3);

        Bus::assertDispatched(CheckChecker::class, fn($job) => $job->checker->id === $run1->id);
        Bus::assertDispatched(CheckChecker::class, fn($job) => $job->checker->id === $run2->id);
        Bus::assertDispatched(CheckChecker::class, fn($job) => $job->checker->id === $run3->id);
    }
}
