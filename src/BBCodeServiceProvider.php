<?php

namespace Salyam\MorningBlue;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BBCodeServiceProvider extends ServiceProvider
{
    /**
     * Registers a singleton BBCode parser object.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BBCode::class, function($app) {return new BBCode();});
    }

    public function boot()
    {
        Blade::directive('BBCode',
            function()
            {
                return '<?php $RawBBCode = <<<\'EO_BBCODE\'';
            }
            );

        Blade::directive('endBBCode',
            function()
            {
                return "\n" . 'EO_BBCODE;' . "\n" . ' echo app(\Salyam\MorningBlue\BBCode::class)->ToHtml($RawBBCode); ?>';
            }
            );
    }
}