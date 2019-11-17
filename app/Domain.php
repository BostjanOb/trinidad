<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    protected $guarded = [];

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }
}
