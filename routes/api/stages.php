<?php

Route::prefix('stages')->group(function () {
    Route::get('/', ['uses' => 'StageController@index','middleware' => ['auth_api:list.tasks']])->name('index.tasks');
});

