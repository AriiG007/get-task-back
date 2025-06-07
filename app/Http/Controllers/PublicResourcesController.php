<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PublicResourcesService;
use Symfony\Component\HttpFoundation\Response;

class PublicResourcesController extends Controller
{
    public function __construct(private PublicResourcesService $publicResourcesService){}

    public function roles()
    {
        $roles = $this->publicResourcesService->roles();
        return  response()->json($roles, Response::HTTP_OK);
    }

    public function teams(){
         $teams = $this->publicResourcesService->teams();
        return  response()->json($teams, Response::HTTP_OK);
    }
}
