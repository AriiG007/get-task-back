<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;
use App\Traits\ModelFilters;
use App\ModelsFilters\TasksFilters;
use App\Models\Task\TaskAssignmentHistory;
use App\Models\Task\TaskStageHistory;

class Task extends Model
{
    use HasUuids, ModelFilters;

    protected $table = 'tasks';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'user_id',
        'stage_id',
        'is_active',
        'cancelled_by',
        'cancellation_reason',
        'completed_at',
    ];

    public static function filterConfigClass(): string
    {
        return TasksFilters::class;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function stagesHistory()
    {
        return $this->hasMany(TaskStageHistory::class);
    }

    public function assignmentsHistory()
    {
        return $this->hasMany(TaskAssignmentHistory::class);
    }
}
