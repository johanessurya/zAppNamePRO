<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Add Validator
        // One or more lower char
        Validator::extend('one_or_more_lower_char', function($attribute, $value, $parameters, $validator) {
          return preg_match("#[0-9]+#", $value);
        });

        // One or more upper char
        Validator::extend('one_or_more_upper_char', function($attribute, $value, $parameters, $validator) {
          return preg_match("#[A-Z]+#", $value);
        });

        // One or more number char
        Validator::extend('one_or_more_number', function($attribute, $value, $parameters, $validator) {
          return preg_match("#[0-9]+#", $value);
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
