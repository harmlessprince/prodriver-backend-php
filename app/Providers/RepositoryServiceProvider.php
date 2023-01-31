<?php

namespace App\Providers;

use App\Repositories\Eloquent\Contracts\EloquentRepositoryInterface;
use App\Repositories\Eloquent\Repository\BaseRepository;
use App\Repositories\Eloquent\Repository\TruckRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(EloquentRepositoryInterface::class, BaseRepository::class);
        // $this->app->bind(BaseRepository::class, TruckRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
