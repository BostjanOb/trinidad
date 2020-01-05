<?php

namespace App\Jobs;

use App\Checker;
use App\CheckerLog;
use App\Checkers\Exceptions\CheckerException;
use App\Events\CheckerResolved;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckChecker implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Checker $checker;

    public function __construct(Checker $checker)
    {
        $this->checker = $checker;
    }

    public function handle()
    {
        $checkerProcess = $this->checker->checker();

        try {
            $checkerProcess->check($this->checker->checkable, $this->checker->arguments ?? []);
            $this->handleSuccess();
        } catch (CheckerException $e) {
            $this->handleException($e);
        }

        $this->checker->next_run = $checkerProcess->nextRun() ?? now()->addMinutes($this->checker->interval);
        $this->checker->save();
    }

    private function handleSuccess(): void
    {
        $this->checker->logs()->unresolved()->get()->each(function (CheckerLog $checkerLog) {
            $checkerLog->resolved_at = now();
            $checkerLog->save();

            event(new CheckerResolved($checkerLog));
        });
    }

    private function handleException(CheckerException $e): void
    {
        $lastLog = $this->checker->logs()->latestUnresolved()->first();

        if ($lastLog !== null
            && $lastLog->level == $e->getCode()
            && $lastLog->message == $e->getMessage()) {
            return;
        }

        $checkerLog = CheckerLog::create([
            'checker_id' => $this->checker->id,
            'message'    => $e->getMessage(),
            'level'      => $e->getCode(),
        ]);

        event(new \App\Events\CheckerException($checkerLog));
    }
}
