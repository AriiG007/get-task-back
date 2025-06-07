<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Requests\Task\TaskIndexRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService){}

    public function index(TaskIndexRequest $request)
    {
        $filters = $request->validated()['filters'] ?? [];
        $paginate = $request->has('paginate');

        $tasks = $this->taskService->getAllTasks($filters, $paginate);
        return response()->json($tasks, Response::HTTP_OK);
    }

    public function show(string $id)
    {
        $task = $this->taskService->getTaskById($id);
        return response()->json($task, Response::HTTP_OK);
    }

    public function store(StoreTaskRequest $request)
    {
        $this->taskService->create($request->validated());
        return response()->json(['message' => 'Task created'], Response::HTTP_CREATED);
    }

    public function update(string $id, UpdateTaskRequest $request)
    {
         if(empty($request->all())) {
            return response()->json(['message' => 'No data provided'], Response::HTTP_BAD_REQUEST);
        }

        $this->taskService->updateTask($id, $$request->validated());
        return response()->json(['message' => 'Task updated'], Response::HTTP_OK);
    }

    public function cancel(string $id, UpdateTaskRequest $request)
    {
        $data = $request->validated();

        $this->taskService->cancelTask($id, $data);
        return response()->json(['message' => 'Task succesfully cancelled'], Response::HTTP_OK);
    }

    public function assignTask(string $id, UpdateTaskRequest $request)
    {
        $data = $request->validated();
        $this->taskService->assignTaskToUser($id, $data);
        return response()->json(['message' => 'Task has been assigned succesfully'], Response::HTTP_OK);
    }

    public function advanceStageTask(string $id)
    {
        $this->taskService->advanceStageTask($id);
        return response()->json(['message' => 'Task has advanced to new stage'], Response::HTTP_OK);
    }

    public function backStageTask(string $id)
    {
        $this->taskService->backStageTask($id);
        return response()->json(['message' => 'Task has moved back a stage'], Response::HTTP_OK);

    }

    public function createFakeTask(){
         $this->taskService->createFakeTask();
        return response()->json(['message' => 'Task created'], Response::HTTP_CREATED);
    }


}
