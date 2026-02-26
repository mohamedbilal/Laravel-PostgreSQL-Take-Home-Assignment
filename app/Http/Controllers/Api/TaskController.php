<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        abort_if($project->user_id !== auth()->id(), 404);

        $task = $project->tasks()->create($request->validated());

        return response()->json(new TaskResource($task), 201);
    }

    public function update(UpdateTaskRequest $request, Project $project, Task $task): JsonResponse
    {
        abort_if($project->user_id !== auth()->id(), 404);
        abort_if($task->project_id !== $project->id, 404);

        $task->update($request->validated());

        return response()->json(new TaskResource($task->fresh()));
    }

    public function destroy(Project $project, Task $task): JsonResponse
    {
        abort_if($project->user_id !== auth()->id(), 404);
        abort_if($task->project_id !== $project->id, 404);

        $this->authorize('delete', $task);
        $task->delete();

        return response()->json(null, 204);
    }
}
