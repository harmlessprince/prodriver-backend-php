<?php

namespace App\Providers;

use App\Models\Guarantor;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Model::preventLazyLoading(!app()->isProduction());
        Relation::enforceMorphMap([
            User::MORPH_NAME => User::class,
            Guarantor::MORPH_NAME => Guarantor::class,
        ]);
        $app_name = Config::get('app.name');
        $client_url = Config::get('app.client_url');
        // $info_mail = Config::get('app.info_mail');
        View::share('app_name', $app_name);
         View::share('client_url', $client_url);
    }
}
