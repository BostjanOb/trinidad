<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Site extends Model
{
    protected $guarded = [];

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }
}
