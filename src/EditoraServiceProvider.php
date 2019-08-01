<?php

namespace Omatech\Editora;

use Illuminate\Support\ServiceProvider;
use Omatech\Editora\Admin\Middleware\EditoraAuth;
use Omatech\Editora\Admin\Providers\HelperServiceProvider;

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
        $this->loadViewsFrom(__DIR__.'/admin/views', 'editora');
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

        $this->mergeConfigFrom(
            __DIR__.'/config/config.php',
            'editora-admin'
        );

        $this->publishes([
            /* Config */
            __DIR__.'/config/config.php' => config_path('editora-admin.php'),

            /* CSS */
            __DIR__.'/admin/csss/welcome.css' => public_path('vendor/editora/css/welcome.css'),
            __DIR__.'/admin/csss/editora.css' => public_path('vendor/editora/css/editora.css'),
            __DIR__.'/admin/csss/grid.css' => public_path('vendor/editora/css/grid.css'),
            __DIR__.'/admin/csss/featherlight.css' => public_path('vendor/editora/css/featherlight.css'),
            __DIR__.'/admin/csss/jquery-ui.min.css' => public_path('vendor/editora/css/jquery-ui.min.css'),
            __DIR__.'/admin/csss/jquery.dataTables.min.css' => public_path('vendor/editora/css/jquery.dataTables.min.css'),
            __DIR__.'/admin/csss/uploadfile.css' => public_path('vendor/editora/css/uploadfile.css'),
            __DIR__.'/admin/fonts/font-awesome' => public_path('vendor/editora/css/font-awesome'),
            __DIR__.'/admin/csss/jquery.Jcrop.css' => public_path('vendor/editora/css/jquery.Jcrop.css'),
            __DIR__.'/admin/csss/dropzone.css' => public_path('vendor/editora/css/dropzone.css'),
            __DIR__.'/admin/csss/color-picker.min.css' => public_path('vendor/editora/css/color-picker.min.css'),
            __DIR__.'/admin/csss/cropper.min.css' => public_path('vendor/editora/css/cropper.min.css'),
            __DIR__.'/admin/csss/auto-complete.css' => public_path('vendor/editora/css/auto-complete.css'),
            __DIR__.'/admin/csss/datepicker.css' => public_path('vendor/editora/css/datepicker.css'),
            __DIR__.'/admin/js/DataTables/datatables.min.css' => public_path('vendor/editora/css/datatables.min.css'),

            /* JS */
            __DIR__.'/admin/js/bootstrap.min.js' => public_path('vendor/editora/js/bootstrap.min.js'),
            __DIR__.'/admin/jss/dropzone.js' => public_path('vendor/editora/js/dropzone.js'),
            __DIR__.'/admin/js/bootstrap.min.js' => public_path('vendor/editora/js/bootstrap.min.js'),
            __DIR__.'/admin/jss/jquery.min.js' => public_path('vendor/editora/js/jquery.min.js'),
            __DIR__.'/admin/jss/color-picker.min.js' => public_path('vendor/editora/js/color-picker.min.js'),
            __DIR__.'/admin/js/DataTables/datatables.min.js' => public_path('vendor/editora/js/datatables.min.js'),
            __DIR__.'/admin/js/maps.js' => public_path('vendor/editora/js/maps.js'),
            __DIR__.'/admin/js/cropper.min.js' => public_path('vendor/editora/js/cropper.min.js'),
            __DIR__.'/admin/js/popper.min.js' => public_path('vendor/editora/js/popper.min.js'),
            __DIR__.'/admin/js/auto-complete.min.js' => public_path('vendor/editora/js/auto-complete.min.js'),
            __DIR__.'/admin/js/jquery-ui.min.js' => public_path('vendor/editora/js/jquery-ui.min.js'),
            __DIR__.'/admin/js/bootstrap-datepicker.js' => public_path('vendor/editora/js/bootstrap-datepicker.js'),
            __DIR__.'/admin/js/jquery.selectric.js' => public_path('vendor/editora/js/jquery.selectric.js'),
            __DIR__.'/admin/js/jquery.mi_tooltip.js' => public_path('vendor/editora/js/jquery.mi_tooltip.js'),
            __DIR__.'/admin/js/jquery.selectric.js' => public_path('vendor/editora/js/jquery.selectric.js'),
            __DIR__.'/admin/jss/ckeditor/' => public_path('vendor/editora/js/ckeditor'),

            /* IMG */
            __DIR__.'/admin/img/omalogin.png' => public_path('vendor/editora/img/omalogin.png'),
            __DIR__.'/admin/img/welcome.jpg' => public_path('vendor/editora/img/welcome.jpg'),
            __DIR__.'/admin/img/omalogo.png' => public_path('vendor/editora/img/omalogo.png'),
            __DIR__.'/admin/img/omalogo-sm.png' => public_path('vendor/editora/img/omalogo-sm.png'),
            __DIR__.'/admin/img/img_no_available.png' => public_path('vendor/editora/img/img_no_available.png'),
            __DIR__.'/admin/img/favicon.ico' => public_path('vendor/editora/img/favicon.ico'),

            /* FONTS */
            __DIR__.'/admin/fonts/editora' => public_path('vendor/editora/fonts'),

        ], 'editora-publish');
    }
}
