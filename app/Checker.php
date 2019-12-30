<?php

namespace App;

use App\Checkers\Exceptions\CheckerException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Checker extends Model
{
    protected $casts = [
        'arguments' => 'json',
    ];

    protected $dates = [
        'last_run',
        'next_run',
    ];

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
            $checker->check($this->checkable, $this->arguments);
            // todo: everything ok, clear old exceptions
        } catch (CheckerException $e) {
            // todo: handle exception
        }

        $this->next_run = $checker->nextRun() ?? $this->calculateNextRun();
        $this->save();
    }

    private function calculateNextRun(): Carbon
    {
        return now()->addMinutes($this->interval);
    }

}
