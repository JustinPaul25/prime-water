<?php

namespace App\Providers;

use App\Rules\CheckNameStaff;
use App\Rules\CheckNameClient;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('check_name_client', function ($attribute, $value, $parameters, $validator) {
            $id = isset($parameters[0]) ? $parameters[0] : null;
            return (new CheckNameClient($id))->passes($attribute, $value);
        });

        Validator::extend('check_name_staff', function ($attribute, $value, $parameters, $validator) {
            $id = isset($parameters[0]) ? $parameters[0] : null;
            return (new CheckNameStaff($id))->passes($attribute, $value);
        });
    }
}
