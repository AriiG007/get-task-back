<?php

Route::prefix('users')->group(function () {

    Route::get('/', ['uses' => 'UserController@index','middleware' => ['auth_api:list.users']])->name('index.users');

    Route::post('store', ['uses' => 'UserController@store','middleware' => ['auth_api:create.users']])->name('store.user');

    Route::get('/{id}', ['uses' => 'UserController@show','middleware' => ['auth_api:list.users']])->name('show.user');

    Route::put('{id}', ['uses' => 'UserController@update','middleware' => ['auth_api:edit.users']])->name('update.user');

    Route::post('{id}/reset-password', ['uses' => 'UserController@resetPassword','middleware' => ['auth_api:reset.password.users']])->name('passwordreset.user');

    Route::post('{id}/approve', ['uses' => 'UserController@approveUser','middleware' => ['auth_api:approve.users']])->name('approve.user');

});

