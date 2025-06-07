<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'user_id' => 'sometimes|required|exists:users,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
