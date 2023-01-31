<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Truck;
use App\Models\Driver;
use App\Models\Company;
use App\Models\Document;
use App\Models\Guarantor;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Repositories\Eloquent\Repository\BaseRepository;
use App\Repositories\Eloquent\Contracts\EloquentRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        // $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
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
            Driver::MORPH_NAME => Driver::class,
            Document::MORPH_NAME => Document::class,
            Company::MORPH_NAME => Company::class,
            Truck::MORPH_NAME => Truck::class,
        ]);
        $app_name = Config::get('app.name');
        $client_url = Config::get('app.client_url');
        // $info_mail = Config::get('app.info_mail');
        View::share('app_name', $app_name);
         View::share('client_url', $client_url);
    }
}
