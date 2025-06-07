<?php
namespace App\Services\Task;

use App\Models\Task;
use App\Models\Task\TaskStageHistory;

class TaskStageHistoryService
{

    /**
     * Crear hostórico de cambio de etapa de tarea.
     * @param Task $task
     * @param string $stageId: id de la etapa a la que se cambia
     * @param string|null $previousStageId: id de la etapa anterior, si es null se asume que es la primera etapa
     */
    public function create(Task $task, string $stageId, string $previousStageId = null): TaskStageHistory
    {

        return $task->stagesHistory()->create([
            'previous_stage_id' => $previousStageId,
            'stage_id' => $stageId,
            'user_id' => auth()->user()->id,
        ]);
    }
}
