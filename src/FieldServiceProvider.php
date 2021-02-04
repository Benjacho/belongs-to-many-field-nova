<?php

namespace Benjacho\BelongsToManyField;

use Laravel\Nova\Nova;
use Laravel\Nova\Events\ServingNova;
use Illuminate\Support\ServiceProvider;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::serving(function (ServingNova $event) {
            Nova::script('BelongsToManyField', __DIR__ . '/../dist/js/field.js');
            Nova::style('BelongsToManyField', __DIR__ . '/../dist/css/field.css');
        });

        $this->app->booted(function () {
            \Route::middleware(['nova'])
                ->prefix('nova-vendor/belongs-to-many-field')
                ->group(__DIR__ . '/../routes/api.php');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
