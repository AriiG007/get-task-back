<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TaskAssignmentHistory extends Model
{
    use HasUuids;

    protected $table = 'tasks_assignments_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'old_user_id',
        'new_user_id',
        'assigned_by',
    ];
}
