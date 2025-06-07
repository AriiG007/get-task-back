<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Team;
use Illuminate\Support\Facades\Log;

class PublicResourcesService{

    public function roles(){
        return Role::whereNot('name', 'super_admin')->get();
    }

    public static function teams()
    {
        return Team::all();
    }

}
