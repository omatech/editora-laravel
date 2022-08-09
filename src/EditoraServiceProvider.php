<?php

namespace Omatech\Editora;

use Illuminate\Support\ServiceProvider;
use Omatech\Editora\Admin\Exceptions\CustomExceptionHandler;
use Omatech\Editora\Admin\Exceptions\CustomExceptionHandlerOld;
use Omatech\Editora\Admin\Middleware\EditoraAuth;
use Omatech\Editora\Admin\Providers\HelperServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

class EditoraServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/Admin/views', 'editora');
    }

    /**
     * Register the application service providers.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(HelperServiceProvider::class);
        $this->app['router']->aliasMiddleware('editoraAuth', EditoraAuth::class);

        $appExceptionHandler = $this->app->make(ExceptionHandlerContract::class);
        if(app()->version() < 7) {
            $this->app->singleton(
                ExceptionHandlerContract::class,
                function ($app) use ($appExceptionHandler) {
                    return new CustomExceptionHandlerOld($app, $appExceptionHandler);
                }
            );
        } else {
            $this->app->singleton(
                ExceptionHandlerContract::class,
                function ($app) use ($appExceptionHandler) {
                    return new CustomExceptionHandler($app, $appExceptionHandler);
                }
            );
        }


        $this->mergeConfigFrom(
            __DIR__.'/config/config.php',
            'editora-admin'
        );

        $this->publishes([
            /* Config */
            __DIR__.'/config/config.php' => config_path('editora-admin.php'),

            /* CSS */
            __DIR__.'/Admin/csss/welcome.css' => public_path('vendor/editora/css/welcome.css'),
            __DIR__.'/Admin/csss/editora.css' => public_path('vendor/editora/css/editora.css'),
            __DIR__.'/Admin/csss/grid.css' => public_path('vendor/editora/css/grid.css'),
            __DIR__.'/Admin/csss/featherlight.css' => public_path('vendor/editora/css/featherlight.css'),
            __DIR__.'/Admin/csss/jquery-ui.min.css' => public_path('vendor/editora/css/jquery-ui.min.css'),
            __DIR__.'/Admin/csss/jquery.dataTables.min.css' => public_path('vendor/editora/css/jquery.dataTables.min.css'),
            __DIR__.'/Admin/csss/uploadfile.css' => public_path('vendor/editora/css/uploadfile.css'),
            __DIR__.'/Admin/csss/jquery.Jcrop.css' => public_path('vendor/editora/css/jquery.Jcrop.css'),
            __DIR__.'/Admin/csss/dropzone.css' => public_path('vendor/editora/css/dropzone.css'),
            __DIR__.'/Admin/csss/color-picker.min.css' => public_path('vendor/editora/css/color-picker.min.css'),
            __DIR__.'/Admin/csss/cropper.min.css' => public_path('vendor/editora/css/cropper.min.css'),
            __DIR__.'/Admin/csss/auto-complete.css' => public_path('vendor/editora/css/auto-complete.css'),
            __DIR__.'/Admin/csss/datepicker.css' => public_path('vendor/editora/css/datepicker.css'),
            __DIR__.'/Admin/js/DataTables/datatables.min.css' => public_path('vendor/editora/css/datatables.min.css'),
            __DIR__.'/Admin/css/extras.css' => public_path('vendor/editora/css/extras.css'),

            /* JS */
            __DIR__.'/Admin/js/bootstrap.min.js' => public_path('vendor/editora/js/bootstrap.min.js'),
            __DIR__.'/Admin/jss/dropzone.js' => public_path('vendor/editora/js/dropzone.js'),
            __DIR__.'/Admin/js/bootstrap.min.js' => public_path('vendor/editora/js/bootstrap.min.js'),
            __DIR__.'/Admin/jss/jquery.min.js' => public_path('vendor/editora/js/jquery.min.js'),
            __DIR__.'/Admin/jss/color-picker.min.js' => public_path('vendor/editora/js/color-picker.min.js'),
            __DIR__.'/Admin/js/DataTables/datatables.min.js' => public_path('vendor/editora/js/datatables.min.js'),
            __DIR__.'/Admin/js/maps.js' => public_path('vendor/editora/js/maps.js'),
            __DIR__.'/Admin/js/cropper.min.js' => public_path('vendor/editora/js/cropper.min.js'),
            __DIR__.'/Admin/js/popper.min.js' => public_path('vendor/editora/js/popper.min.js'),
            __DIR__.'/Admin/js/auto-complete.min.js' => public_path('vendor/editora/js/auto-complete.min.js'),
            __DIR__.'/Admin/js/jquery-ui.min.js' => public_path('vendor/editora/js/jquery-ui.min.js'),
            __DIR__.'/Admin/js/bootstrap-datepicker.js' => public_path('vendor/editora/js/bootstrap-datepicker.js'),
            __DIR__.'/Admin/js/jquery.selectric.js' => public_path('vendor/editora/js/jquery.selectric.js'),
            __DIR__.'/Admin/js/jquery.mi_tooltip.js' => public_path('vendor/editora/js/jquery.mi_tooltip.js'),
            __DIR__.'/Admin/js/jquery.selectric.js' => public_path('vendor/editora/js/jquery.selectric.js'),
            __DIR__.'/Admin/jss/ckeditor/' => public_path('vendor/editora/js/ckeditor'),

            /* IMG */
            __DIR__.'/Admin/img/omalogin.png' => public_path('vendor/editora/img/omalogin.png'),
            __DIR__.'/Admin/img/welcome.jpg' => public_path('vendor/editora/img/welcome.jpg'),
            __DIR__.'/Admin/img/omalogo.png' => public_path('vendor/editora/img/omalogo.png'),
            __DIR__.'/Admin/img/omalogo-sm.png' => public_path('vendor/editora/img/omalogo-sm.png'),
            __DIR__.'/Admin/img/img_no_available.png' => public_path('vendor/editora/img/img_no_available.png'),
            __DIR__.'/Admin/img/favicon.ico' => public_path('vendor/editora/img/favicon.ico'),

            /* FONTS */
            __DIR__.'/Admin/fonts/editora' => public_path('vendor/editora/fonts'),
            __DIR__.'/Admin/fonts/font-awesome' => public_path('vendor/editora/css/font-awesome'),
            __DIR__.'/Admin/fonts/material-design' => public_path('vendor/editora/fonts'),

            /* Frontend Tools */
            __DIR__.'/EditoraFront/' => app_path('EditoraFront'),

        ], 'editora-publish');
    }
}
