<?php

namespace App;

use App\Checkers\Exceptions\CheckerException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checker extends Model
{
    protected $casts = [
        'arguments' => 'json',
    ];

    protected $dates = [
        'last_run',
        'next_run',
    ];

    public function logs(): HasMany
    {
        return $this->hasMany(CheckerLog::class);
    }

    public function checkable()
    {
        return $this->morphTo('checkable');
    }

    public function checker()
    {
        return app($this->checker);
    }

    public function check()
    {
        $checker = $this->checker();

        try {
            $checker->check($this->checkable, $this->arguments ?? []);
            // todo: everything ok, clear old exceptions
        } catch (CheckerException $e) {
            $this->handleException($e);
        }

        $this->next_run = $checker->nextRun() ?? $this->calculateNextRun();
        $this->save();
    }

    private function handleException(CheckerException $e)
    {
        $lastLog = $this->logs()->latestUnresolved()->first();

        if ($lastLog !== null
            && $lastLog->level == $e->getCode()
            && $lastLog->message == $e->getMessage()) {
            return;
        }

        CheckerLog::create([
            'checker_id' => $this->id,
            'message'    => $e->getMessage(),
            'level'      => $e->getCode(),
        ]);
    }

    private function calculateNextRun(): Carbon
    {
        return now()->addMinutes($this->interval);
    }

}
