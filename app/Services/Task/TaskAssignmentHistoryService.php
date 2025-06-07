<?php
namespace App\Services\Task;

use App\Models\Task;
use App\Models\Task\TaskAssignmentHistory;

class TaskAssignmentHistoryService
{

    /**
     * Crear historico de cambio de asignacion de tarea.
     * @param Task $task
     * @param string $newUserId: id del nuevo usuario asignado a la tarea
     * @param string|null $oldUserId: id del usuario anterior asignado a la tarea, si es null se asume que no había un usuario asignado
     * @return TaskAssignmentHistory
     */
    public function create(Task $task, string $newUserId, string $oldUserId = null): TaskAssignmentHistory
    {

        return $task->assignmentsHistory()->create([
            'old_user_id' => $oldUserId,
            'new_user_id' => $newUserId,
            'assigned_by' => auth()->user()->id,
        ]);
    }

}
