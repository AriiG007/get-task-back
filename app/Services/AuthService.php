<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CustomException;

class AuthService{


     public function auth(array $credentials)
    {

        $user = User::where('email', $credentials['email'])->first();

        Log::info('User found: ', ['user' => $user]);

        if (!$user || !$user->is_validated || $user->status == 'inactive') {
            throw new CustomException('The user is pending approval or is not active.');
        }

        if (!$token = auth()->attempt($credentials)) {
            throw new CustomException('Invalid username or password');
        }

        $user = auth()->user()->load('permissions');

        return [
            'access_token' => $this->getTokenResponse($token),
            'user' => $user,
        ];
    }


    private function getTokenResponse($token)
    {
        return [
            'token' => $token,
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }


}
