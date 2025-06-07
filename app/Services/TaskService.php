<?php

namespace App\Services;
use App\Models\Task;
use App\Services\Task\TaskAssignmentHistoryService;
use App\Services\Task\TaskStageHistoryService;
use Illuminate\Support\Facades\DB;
use App\Models\Stage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Carbon\Carbon;
use App\Notifications\TaskCompletedNotification;
use App\Models\User;
use App\Exceptions\CustomException;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

class TaskService{

    public function __construct(private TaskAssignmentHistoryService $assignmentHistoryService, private TaskStageHistoryService $stageHistoryService){}


    /**
     * Para buscar/obtener la tarea con el id dado, se aplican ciertas validaciones para asegurarse que solo encuentre la tarea,
     * si el usuario que consulta puede verla por permiso o porque le pertenezca.
     */
     public function getTaskById(string $id): Task
    {

        $authUser = auth()->user();

        /**
         * si el usuario autenticado es super admin, solo busca la tarea por id (el super admin puede acceder a todas las tareas)
         */
        if($authUser->isSuperAdmin()){
            return Task::findOrFail($id);
        }

        /**
         * Si el usuario no es super admin pero tiene permiso para listar todas las tareas
         * aplica filtro para validar que la tarea a la que intenta acceder le perteneza a un miembro de su equipo
         */
        if($authUser->hasPermission('list.all.tasks') ){
            return Task::whereHas('user', function ($query) use($authUser){
                return $query->where('team_id', $authUser->team_id);
            })->findOrFail($id);
        }

        /**
         * Si las validaciones anteriores no se cumplen, se aplica filtro para validar que la tarea que se intenta acceder
         * le pertenezca al usuario autenticado
         */
        return Task::where('user_id', $authUser->id)->findOrFail($id);
    }


    public function getAllTasks($filters, $paginate = false)
    {
        $query = Task::query();

         /**
          * Validar que tareas puede ver el usuario autenticado
         * super admin -> puede ver todas las tareas sin importar su team (no se aplica ningun filtro)
         * si no es un super admin -> valida que tenga permiso para listar todas las tareas y solo muestra las de su team
         * si no tiene permiso de listar todas las tareas, solo lista las suyas significa que solo es un team member
         * o un usuario restringido y solo puede ver las tareas a las que esta asignado
         */

         $authUser = auth()->user();
         if(!$authUser->isSuperAdmin()){

            if($authUser->permissions()->where('permission', 'list.all.tasks')->exists() ){
                $query->whereHas('user', function ($query) use($authUser){
                    $query->where('team_id', $authUser->team_id);
                });

            }else{
                $query->where('user_id', $authUser->id);
            }
        }

        $query->applyFilters($filters);
        $query->orderBy('created_at', 'desc');

        if($paginate)
            return $query->paginate(10);

        return $query->get();
    }


    // Crear tarea
    public function create(array $data): Task
    {
        /**
         * Si el request contiene user_id para la tarea que se esta creando,
         * Validar que el usuario autenticado pueda asignar tareas a otros usuarios, si no lanzar una excepción.
         */
        $this->validateTaskAssignment($data);

        try {
               DB::beginTransaction();

                $authUser = auth()->user();
                $stage = StageService::getFirstStage();

               $task = Task::create([
                   ...$data,
                   'user_id' => isset($data['user_id']) ? $data['user_id'] : $authUser->id,
                   'created_by' => $authUser->id,
                   'stage_id' => $stage->id,
               ]);

               $this->assignmentHistoryService->create($task, $task->user_id, null);
               $this->stageHistoryService->create($task, $stage->id, null);

               DB::commit();

               return $task;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw new CustomException('Unexpected error occurred while creating task.');
        }

    }

    public function updateTask(string $id, array $data): Task
    {

        Log::info('Updating task with ID: ' . $id, ['data' => $data]);

         /** Obtener task y validar estaus de la tarea,
         * si esta cancelada o ya se termino lanza un excepcion. para reestringir editar de tarea */
        $task = $this->getTaskById($id);
        $this->hasValidStatusForChanges($task);

        try {
               DB::beginTransaction();
               /**
                * Actualizar tarea, solo de puede editar nombre y descripcion
                * la asignacion, cancelacion y cambio de estado solo se pueden hacer en su propio metodo.
                */

               $task->update(Arr::only($data, ['name','description']));

               DB::commit();

               return $task;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw new CustomException('Unexpected error occurred while updating task.');
        }
    }

    public function advanceStageTask(string $id): Task
    {

        /** Obtener task y validar estaus de la tarea,
         * si esta cancelada o ya se termino lanza un excepcion. para reestringir cambo de stage */

        $task = $this->getTaskById($id);
        $this->hasValidStatusForChanges($task);

        try {
            DB::beginTransaction();

            $currentStage = Stage::find($task->stage_id);
            $nextStage = $currentStage->nextStage();

            /**
             * Validar si el nuevo stage es el ultimo (no hay ningun stage despues de este)
             */
            $isLastStage = !$nextStage->nextStage();

            $dataUpdates = !$isLastStage ?  ['stage_id' => $nextStage->id] :
                            ['completed_at' => Carbon::now()->toDateTimeString(),'stage_id' => $nextStage->id];

            $task->update($dataUpdates);

            // Registrar el cambio de etapa en el historial
            $this->stageHistoryService->create($task, $nextStage->id, $currentStage->id);


            //enviar correo de tarea terminada si este es el ultimo stage
            if($isLastStage){
                $authUser = auth()->user();
                $userTask = $task->user;
                TaskCompletedNotification::sendMail($userTask->email, $task->name, $authUser->name);
            }

            DB::commit();

            return $task;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw new CustomException('Unexpected error occurred while moving task stage.');
        }
    }

    public function backStageTask(string $id): Task
    {

        /** Obtener task y validar estaus de la tarea,
         * si esta cancelada o ya se termino lanza un excepcion. para reestringir cambo de stage */

        $task = $this->getTaskById($id);
        $this->hasValidStatusForChanges($task);

        try {
            DB::beginTransaction();

            $currentStage = Stage::find($task->stage_id);
            $previousStage = $currentStage->previousStage();

            // Actualizar la tarea con el nuevo stage
            $task->update(['stage_id' => $previousStage->id]);

            // Registrar el cambio de etapa en el historial
            $this->stageHistoryService->create($task, $previousStage->id, $currentStage->id);

            DB::commit();

            return $task;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw new CustomException('Unexpected error occurred while moving task stage.');
        }
    }

    public function assignTaskToUser(string $id, array $data): Task
    {

         /** Obtener task y validar estaus de la tarea,
          * * si esta cancelada o ya se termino lanza un excepcion. para reestringir asignacion de tarea */
        $task = $this->getTaskById($id);
        $this->hasValidStatusForChanges($task);

        /** Validar que el usuario autenticado pueda asignar tareas a otros usuarios,
         * si no lanzar una excepción.*/
        $this->validateTaskAssignment($data);

        try {
            DB::beginTransaction();

            $oldUserId = $task->user_id;
            // Actualizar la tarea con el nuevo usuario asignado
            $task->update(['user_id' => $data['user_id']]);

            // Registrar el cambio de asignación en el historial
            $this->assignmentHistoryService->create($task, $data['user_id'], $oldUserId);

            DB::commit();

            return $task;
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw new CustomException('Unexpected error occurred while assigning task to user.');
        }
    }

    public function cancelTask(string $id, array $data)
    {

        /** Obtener task y validar estaus de la tarea,
         * si esta cancelada o ya se termino lanza un excepcion. */
        $task = $this->getTaskById($id);
        $this->hasValidStatusForChanges($task);

        // Actualizar la tarea a estado cancelado
        $task->update([
            'is_active' => false,
            'cancelled_by' => auth()->user()->id,
            'cancellation_reason' => $data['cancellation_reason'],
        ]);
    }

    /**
     * Validar que el usuario autenticado pueda asignar la tarea.
     * Tiene permiso si:
     * 1. la tarea es para el mismo es decir el request user_id es el suyo
     * 2. es super admin
     * 3. tiene permisos para asignar tareas y pertenece al mismo team del usuario al que pretende asignar la tarea
     * en caso de no cumplirse estas validaciones lanzara una excepcion
     * @param array $data
     */
    private function validateTaskAssignment(array $data)
    {
        $authUser = auth()->user();


        if(isset($data['user_id'])){

            $taskUser = User::find($data['user_id']);

            $hasPermission = $authUser->isSuperAdmin() ||
                             ($authUser->hasPermission('assign.tasks') && $taskUser->team_id === $authUser->team_id);


            if($data['user_id'] !== $authUser->id && !$hasPermission ){
               throw new CustomException('You do not have permission to assign tasks to other users.');
           }
        }
    }


    /**
     * Validar estaus de la tarea,  si esta cancelada o completada, editar o mover de stage
     */

    private function hasValidStatusForChanges(Task $task){

        if(!$task->is_active){
            throw  new CustomException('The task is cancelled.');
        }

        if(isset($task->completed_at)){
             throw  new CustomException('The task has already been completed.');
        }

    }

    public function createFakeTask(){
        try {

            DB::beginTransaction();
            $authUser = auth()->user();
            $stage = StageService::getFirstStage();
            $task = Task::create([
            'name' => "Fake task ".Str::random(10),
            'description'=> "Description: ".Str::random(100),
            'user_id' =>  $authUser->id,
            'created_by' => $authUser->id,
            'stage_id' => $stage->id,
            ]);

            $this->assignmentHistoryService->create($task, $task->user_id, null);
            $this->stageHistoryService->create($task, $stage->id, null);

            DB::commit();

            return $task;

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            throw new CustomException('Unexpected error occurred while creating task.');
        }

    }


}
