<?php

namespace App\Http\ViewComposers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Member;
use Auth;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        View::composer('*', 'App\Http\ViewComposers\MenusComposer');
        View::composer(['member','member/*','admin','admin/*', 'production-partner', 'production-partner/*'], 'App\Http\ViewComposers\MessageComposer');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}