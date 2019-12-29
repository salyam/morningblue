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
            function($expression)
            {
                return "<?php echo app(\Salyam\MorningBlue\BBCode::class)->ToHtml($expression); ?>";
            }
            );
    }
}