<?php

Route::prefix('public-resources')->group(function () {

   Route::get('roles', ['uses' => 'PublicResourcesController@roles']);
   Route::get('teams', ['uses' => 'PublicResourcesController@teams']);

});
