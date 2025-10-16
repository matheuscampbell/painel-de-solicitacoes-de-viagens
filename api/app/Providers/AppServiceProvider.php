<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

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

        Validator::extend('numero', function ($attribute, $value) {
            return is_numeric($value);
        });

        Validator::replacer('numero', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'O campo :attribute deve ser um número válido');
        });
    }
}
