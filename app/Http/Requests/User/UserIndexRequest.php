<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\IndexModelRequest;
use App\Models\User;

class UserIndexRequest extends IndexModelRequest
{
    protected string $model = User::class;

}
