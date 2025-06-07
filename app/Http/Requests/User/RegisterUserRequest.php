<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
            'team_id' => 'required|exists:teams,id',
            // solo obligatorio si es autoregistro
            'password' => $this->routeIs('auth.register')
                ? 'required|string|min:8|confirmed'
                : 'nullable|string',

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
