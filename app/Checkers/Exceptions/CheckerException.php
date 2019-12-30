<?php

namespace App\Checkers\Exceptions;

use Carbon\Carbon;

class CheckerException extends \Exception
{
    const EMERGENCY = 1;
    const ALERT = 2;
    const CRITICAL = 3;
    const WARNING = 4;
    const NOTICE = 5;

    public Carbon $resolved_at;

    public function markResolved(): self
    {
        $this->resolved_at = now();
        return $this;
    }
}
