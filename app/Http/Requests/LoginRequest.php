<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {

        $rules = [
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ];


        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
