<?php

namespace App\Console\Commands;

use App\Checker;
use App\Jobs\CheckChecker;
use Illuminate\Console\Command;

class CheckersRun extends Command
{
    protected $signature = 'checkers:run';
    protected $description = 'Run scheduled checkers';

    public function handle()
    {
        Checker::where('next_run', '<=', now())
            ->each(fn(Checker $checker) => CheckChecker::dispatch($checker));
    }
}
