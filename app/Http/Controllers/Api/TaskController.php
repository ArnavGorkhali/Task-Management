<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FunctionOrderRequest;
use App\Http\Requests\TaskOrderRequest;
use App\Http\Requests\TaskRequest;
use App\Models\EventFunction;
use App\Models\Task;
use App\Repositories\Task\TaskRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * @var TaskRepositoryInterface
     */
    private $functionRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $tasks = $this->taskRepository->getAll();
        return success('Tasks', $tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TaskRequest $request
     * @return JsonResponse
     */
    public function store(TaskRequest $request)
    {
        $response = $this->taskRepository->create($request);
        if($response instanceof Task) {
            return success(($response->parent_id?"Subtask":"Task").' created successfully.', $response);
        }
        return failure('Failed to created task.');
    }

    /**
     * Display the specified resource.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Task $task)
    {
        $response = $this->taskRepository->show($task);
        return success('Task', !$response->parent_id ? $response->load('subtasks', 'user:id,name'): $response);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param TaskRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(TaskRequest $request, Task $task)
    {
        $response = $this->taskRepository->update($request, $task);
        if($response instanceof Task) {
            return success('Task updated successfully.', $response);
        }
        return failure('Failed to updated task.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task)
    {
        $response = $this->taskRepository->delete($task);
        if($response instanceof Task) {
            return success('Task deleted successfully.', $response);
        }
        return failure('Failed to deleted task.');
    }

    public function order(TaskOrderRequest $request)
    {
        $response = $this->taskRepository->orderTasks($request);
        if($response instanceof Task) {
            return success('Tasks ordered successfully');
        }
        return failure('Tasks failed to order.');
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'task_id' => 'required|int|exists:tasks,id',
            'status' => 'required|string|'.Rule::in(config('options.event_status'))
        ]);
        $response = $this->taskRepository->changeStatus($request);
        if($response instanceof Task) {
            return success('Task '.$request->status, $response);
        }
        return failure('Failed to change status');
    }

    public function changePriority(Request $request)
    {
        $request->validate([
            'task_id' => 'required|int|exists:tasks,id',
            'priority' => 'required|string|'.Rule::in(config('options.event_priority'))
        ]);
        $response = $this->taskRepository->changePriority($request);
        if($response instanceof Task) {
            return success('Task priority set to '.$request->priority, $response);
        }
        return failure('Failed to change status');
    }

    public function taskSummary(Request $request)
    {
        $response = $this->taskRepository->taskSummary($request);
        return success('Tasks summary', $response);
    }

    public function dashboardTasks(Request $request)
    {
        $response = $this->taskRepository->dashboardTasksList($request);
        return success('Home Tasks', $response);
    }
}
