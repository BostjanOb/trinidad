<?php

namespace App\Checkers\Exceptions;

class Emergency extends CheckerException
{
    public static function create(string $message)
    {
        return new self($message, CheckerException::EMERGENCY);
    }
}
