<?php

namespace App;

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
}
