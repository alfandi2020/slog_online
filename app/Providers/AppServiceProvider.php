<?php

namespace App\Providers;

use DB;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require_once app_path().'/Services/helpers.php';
        Validator::extend('not_exists', function ($attribute, $value, $parameters) {
            return DB::table($parameters[0])
                ->where($parameters[1], $value)
                ->count() < 1;
        });

        // $n = 0;
        // \DB::listen(function ($sql) use (&$n) {
        //     $n++;
        //     if ($n > 30 && config('app.debug')) {
        //         // session(["test_warning" => "High number of SQL queries ($n)"]);
        //         session(["test_warning" => "($n)"]);
        //     }
        // });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
        }
    }
}
