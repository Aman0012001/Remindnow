<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use View;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        $youtube   = Setting::where('key','Social.youtube')->first();
        $facebook  = Setting::where('key','Social.facebook')->first();
        $twitter   = Setting::where('key','Social.twitter')->first();
        $linkedin  = Setting::where('key','Social.linkedin')->first();
        $copyright  = Setting::where('key','Site.right')->first();
        View::share('youtube', $youtube);
        View::share('facebook', $facebook);
        View::share('twitter', $twitter);
        View::share('linkedin', $linkedin);
        View::share('copyright', $copyright);

    }
}
