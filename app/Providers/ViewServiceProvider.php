<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register()
    {
        \View::share('feather', function ($icon, int $size = 4, $class = '') {
            return "<svg class=\"feather w-{$size} h-{$size} {$class}\"><use xlink:href=\"/images/feather.svg#{$icon}\"></use></svg>";
        });

        \View::share('fa', function ($icon, int $size = 4, $class = '') {
            return "<svg class=\"w-{$size} h-{$size} {$class}\"><use xlink:href=\"/images/fa.svg#{$icon}\"></use></svg>";
        });
    }
}
