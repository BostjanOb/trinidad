<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckerLog extends Model
{
    protected $guarded = [];

    protected $dates = [
        'resolved_at',
    ];

    public function checker(): BelongsTo
    {
        return $this->belongsTo(Checker::class);
    }

    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    public function scopeLatestUnresolved($query)
    {
        return $this->scopeUnresolved($query)->orderBy('created_at', 'desc')->orderBy('id', 'desc');
    }
}
