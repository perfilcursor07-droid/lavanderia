<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Helper global para tratar propriedades que podem ser null
        if (!function_exists('safe_property')) {
            function safe_property($object, $property, $default = 'Não definido') {
                return $object && $object->$property ? $object->$property : $default;
            }
        }
    }
}
