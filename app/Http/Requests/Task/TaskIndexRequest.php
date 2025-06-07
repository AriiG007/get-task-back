<?php

namespace App\Http\Requests\Task;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\IndexModelRequest;
use App\Models\Task;

class TaskIndexRequest extends IndexModelRequest
{
    protected string $model = Task::class;

}
