<?php

namespace App\Checkers;

use Carbon\Carbon;

interface Checker
{
    public function check($model, array $arguments);

    public function nextRun(): ?Carbon;
}
