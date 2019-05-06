<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    protected $guarded = [];

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function setIpAttribute($ip)
    {
        \Validator::make(['ip' => $ip], ['ip' => 'ipv4'])->validate();

        $this->attributes['name'] = $this->attributes['name'] ?? $ip;
        $this->attributes['ip'] = $ip;
    }
}
