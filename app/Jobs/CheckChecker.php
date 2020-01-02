<?php

namespace App\Jobs;

use App\Checker;
use App\CheckerLog;
use App\Checkers\Exceptions\CheckerException;
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

    private function handleSuccess()
    {
        $this->checker->logs()->unresolved()->update(['resolved_at' => now()]);
    }

    private function handleException(CheckerException $e)
    {
        $lastLog = $this->checker->logs()->latestUnresolved()->first();

        if ($lastLog !== null
            && $lastLog->level == $e->getCode()
            && $lastLog->message == $e->getMessage()) {
            return;
        }

        CheckerLog::create([
            'checker_id' => $this->checker->id,
            'message'    => $e->getMessage(),
            'level'      => $e->getCode(),
        ]);
    }
}
