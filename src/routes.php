<?php

/*
|--------------------------------------------------------------------------
| ByPass to EditoraAdmin
|--------------------------------------------------------------------------
|
*/
Route::group(['prefix' => config('editora-admin.route.prefix'), 'middleware' => ['web']], function () {
    Route::any('/logout', 'Omatech\Editora\app\Http\Controllers\AuthController@logout')->name('editora.logout');
    Route::any('/login', 'Omatech\Editora\app\Http\Controllers\AuthController@login')->name('editora.login');
    Route::any('/maintenance_mode', 'Omatech\Editora\app\Http\Controllers\AuthController@maintenance_mode')->name('editora.maintenance_mode');
    Route::any('/', 'Omatech\Editora\app\Http\Controllers\Instance@get_main')->name('editora.get_main');
    
    Route::any('/configure', 'Omatech\Editora\app\Http\Controllers\AuthController@login')->name('editora.configure');
    Route::any('/search', 'Omatech\Editora\app\Http\Controllers\Instance@get_main')->name('editora.search');
    Route::any('/instance/{class_id}/{inst_id}', 'Omatech\Editora\app\Http\Controllers\Instance@view_instance')->name('editora.view_instance');
    Route::any('/class/{class_id}', 'Omatech\Editora\app\Http\Controllers\Instance@view_class_instances')->name('editora.view_class_instances');
    Route::any('/delete_favorite', 'Omatech\Editora\app\Http\Controllers\Instance@get_main')->name('editora.delete_favorite');
    Route::any('/ajax_actions', 'Omatech\Editora\app\Http\Controllers\Instance@get_main')->name('editora.ajax_actions');
    


    // Route::any('/{path}', 'Omatech\Editora\Admin\Controller@init')->where(['path' => '.+'])->name('editora.action');
});
