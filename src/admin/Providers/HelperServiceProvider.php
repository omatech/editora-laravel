<?php

namespace Omatech\Editora\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function register()
    {
        $helpers = glob(__DIR__.'/../app/Helpers/*.php');
        foreach ($helpers as $filename){
            require_once($filename);
        }
    }

}