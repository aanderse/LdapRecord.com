<?php

namespace App\Providers;

use App\Documentation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Execute any operations required upon boot.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('app', function ($view) {
            $view->with('versions', Documentation::getDocVersions());
        });
    }
}
