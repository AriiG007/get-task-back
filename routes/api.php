<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::group(['prefix'=>'v1','namespace'=>'App\Http\Controllers'], function() {
    require ('api/auth.php');
    require ('api/users.php');
    require ('api/tasks.php');
    require ('api/stages.php');
    require ('api/publicResources.php');
});
