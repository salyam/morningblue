<?php

namespace Salyam\MorningBlue;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class BBCodeServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BBCode::class, function($app) {return new BBCode();});
    }

    public function provides()
    {
        return [BBCode::class];
    }
}