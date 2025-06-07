<?php

namespace App\Services;

use App\Models\Stage;
use Illuminate\Support\Facades\Log;

class StageService{

    public function getAllStages(){
        return Stage::all();
    }

    public static function getFirstStage()
    {
        return Stage::orderBy('order', 'asc')->first();
    }

    public static function getLastStage()
    {
        return Stage::orderBy('order', 'desc')->first();
    }



}
