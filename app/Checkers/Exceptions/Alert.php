<?php

namespace App\Checkers\Exceptions;

class Alert extends CheckerException
{
    public static function create(string $message)
    {
        return  new self($message, CheckerException::ALERT);
    }
}
