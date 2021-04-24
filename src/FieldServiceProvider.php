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
            Nova::script('BelongsToManyField', __DIR__.'/../dist/js/field.js');
            Nova::style('BelongsToManyField', __DIR__.'/../dist/css/field.css');
        });

        $this->app->booted(function () {
            \Route::middleware(['nova'])
                ->prefix('nova-vendor/belongs-to-many-field')
                ->group(__DIR__.'/../routes/api.php');
        });

        $this->publishes([
            __DIR__.'/../resources/lang/' => resource_path('lang/vendor/belongs-to-many-field-nova'),
        ]);
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang','belongs-to-many-field-nova');
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
