<?php

Route::prefix('tasks')->group(function () {

    Route::get('/', ['uses' => 'TaskController@index','middleware' => ['auth_api:list.tasks']])->name('index.tasks');

    Route::post('store', ['uses' => 'TaskController@store','middleware' => ['auth_api:create.tasks']])->name('store.task');

    /** Genera tareas fake para el usaurio autenticado no necesita enviar parametros */
    Route::post('create-fake-task', ['uses' => 'TaskController@createFakeTask','middleware' => ['auth_api:create.tasks']])->name('store.task');

    Route::get('/{id}', ['uses' => 'TaskController@show','middleware' => ['auth_api:list.tasks']])->name('show.task');

    Route::put('{id}', ['uses' => 'TaskController@update','middleware' => ['auth_api:edit.tasks']])->name('update.task');

    Route::post('{id}/advance', ['uses' => 'TaskController@advanceStageTask','middleware' => ['auth_api:edit.tasks']])->name('advance.stage.task');

    Route::post('{id}/back', ['uses' => 'TaskController@backStageTask','middleware' => ['auth_api:edit.tasks']])->name('back.stage.task');

    Route::post('{id}/cancel', ['uses' => 'TaskController@cancel','middleware' => ['auth_api:edit.tasks']])->name('cancel.task');

    Route::post('{id}/assign', ['uses' => 'TaskController@assignTask','middleware' => ['auth_api:assign.tasks']])->name('assign.task');

});

