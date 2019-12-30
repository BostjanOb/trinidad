<?php

namespace Tests\Stubs\Checkers;

use App\Checkers\Checker;
use App\Checkers\Exceptions\Alert;
use Carbon\Carbon;

class AlertStub implements Checker
{
    public function check($model, array $arguments)
    {
        throw Alert::create('Some Error');
    }

    public function nextRun(): ?Carbon
    {
        return null;
    }
}
