<?php

/*
|--------------------------------------------------------------------------
| ByPass to EditoraAdmin
|--------------------------------------------------------------------------
|
*/

Route::group(['prefix' => config('editora-admin.route.prefix'), 'middleware' => ['web']], function () {
    Route::any('/', 'Omatech\Editora\Admin\Controller@init');
    Route::any('/{path}', 'Omatech\Editora\Admin\Controller@init')->where(['path' => '.+'])->name('editora.action');
});
