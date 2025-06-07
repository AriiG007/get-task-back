<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {

        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'role_id' => 'sometimes|required|exists:roles,id',
            'team_id' => 'sometimes|required|exists:teams,id',
            'status' => 'sometimes|required|in:active,inactive',

        ];

        // solo obligatorio si es la ruta es de cambio de contraseña
        if ($this->routeIs('auth.password_reset')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
