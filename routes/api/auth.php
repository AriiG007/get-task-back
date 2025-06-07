<?php

Route::prefix('auth')->group(function () {

   Route::post('login', ['uses' => 'AuthController@login']);
   Route::post('logout', ['uses' => 'AuthController@logout']);
   Route::post('refresh', ['uses' => 'AuthController@refresh']);
   Route::post('me', ['uses' => 'AuthController@me']);

    // User registration route

   Route::post('register',  ['uses' => 'UserController@register'])->name('auth.register');


});
