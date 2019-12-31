<?php

namespace App\Domain\Checkers;

use App\Checkers\Checker;
use Carbon\Carbon;

class ExpireDate implements Checker
{
    public function check($model, array $arguments)
    {
        // TODO: Implement check() method.
    }

    public function nextRun(): ?Carbon
    {
        // TODO: Implement nextRun() method.
    }
}
