<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StageService;
use Symfony\Component\HttpFoundation\Response;

class StageController extends Controller
{
    public function __construct(private StageService $stageService){}

    public function index()
    {
        $stages = $this->stageService->getAllStages();
        return  response()->json($stages, Response::HTTP_OK);
    }
}
