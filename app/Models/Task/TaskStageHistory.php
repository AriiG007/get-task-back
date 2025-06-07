<?php

namespace App\Models\Task;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;


class TaskStageHistory extends Model
{
    use HasUuids;

    protected $table = 'tasks_stages_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_id',
        'stage_id',
        'previous_stage_id',
        'user_id', // usuario que realizó el cambio de etapa
    ];
}
