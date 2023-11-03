<?php

namespace App\Repositories\Task;

use App\Models\Event;
use App\Models\EventFunction;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TaskRepository implements TaskRepositoryInterface
{

    public function getAll()
    {
        return Task::get();
    }

    public function create($request)
    {
        $task = Task::select("*")->create($request->input());
        return Task::with("parent:id,function_id")->find($task->id);
    }

    public function show($task)
    {
        $task = Task::withCount('subtasks', 'complete_subtasks', 'vendors')->findOrFail($task->id);
        return $task;
    }

    public function update($request, $task)
    {
        $task->update($request->input());
        return Task::with("parent:id,function_id")->find($task->id);
    }

    public function delete($task)
    {
        $task->delete();
        return Task::withoutGlobalScopes()->with("parent:id,function_id")->find($task->id);
    }

    public function orderTasks($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->tasks as $key => $task) {
                $eventTask = Task::whereId($task['id'])
                    ->first();
                if ($eventTask) {
                    $eventTask->order = $task['order'];
                    $eventTask->save();
                }
            }
            DB::commit();
            return $eventTask;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function changeStatus($request)
    {
        $task = Task::find($request->task_id);
        $task->status = $request->status;
        $task->save();
        return Task::with("parent:id,function_id")->find($task->id);
    }

    public function updateParentStatus($task)
    {
        if($task->parent_id){
            $parent = $task->parent;
            if($parent){
                $tasks = $parent->subtasks()->where(function ($q){
                    $q->where('status', 'incomplete')
                        ->orWhere('status', null);
                })->count();
                if($tasks == 0) {
                    $parent->status = "complete";
                    $parent->save();
                } else{
                    $parent->status = "incomplete";
                    $parent->save();
                }
            }
        }else{
            $function = $task->function;
            if($function){
                $tasks = $function->tasks()->where(function ($q){
                    $q->where('status', null)
                        ->orWhere('status', 'incomplete');
                })->count();
                if($tasks == 0) {
                    $function->status = "complete";
                    $function->save();
                } else {
                    $function->status = "incomplete";
                    $function->save();
                }
            }
        }
    }

    public function changePriority($request)
    {
        $task = Task::find($request->task_id);
        $task->priority = $request->priority;
        $task->save();
        return Task::with("parent:id,function_id")->find($task->id);
    }

    public function getWorkList($request)
    {
        $date = $request->date ?: now()->format('Y-m-d');

        $tasks = Task::select('id', 'name', 'note', 'function_id', 'due_date', 'status', 'user_id')
            ->with('event:events.id,events.name')
            ->with('user:id,name')
            ->with('function:id,name')
            ->where('tasks.parent_id', null)
            ->orderBy('due_date');
        if($request->type == "ongoing") {
            $tasks->isOngoing($date);
        }elseif($request->type == 'todo'){
            $tasks->isTodo()->whereDate('tasks.due_date', $date);
        }else{
            $tasks->isComplete()->whereDate('tasks.due_date', $date);
        }
        $tasks = $tasks->get();

        $event_ids = array_unique($tasks->pluck('event.id')->toArray());
        $data = [];
        foreach ($event_ids as $event_id) {
            $event = Event::select('id', 'name', 'category')->find($event_id);
            if($event){
                $event->tasks = collect($tasks->where('event.id', $event_id))->values();
                $data[] = $event;
            }
        }
//        foreach ($tasks as $task) {
//
//        }

        return $data;
    }

    public function taskSummary($request)
    {
        $data = [];
        $types = [
            'todo',
            'ongoing',
            'complete'
        ];
        $today = $request->date?:now()->format('Y-m-d');

        foreach ($types as $type) {
            $tasks = Task::select('id', 'name', 'note', 'function_id', 'due_date', 'status')
//                ->with('event:events.id,events.name')
                    ->has('event')
                ->where('tasks.parent_id', null);
            if($type == "ongoing") {
                $tasks->isOngoing($today);
            }elseif($type == 'todo'){
                $tasks->isTodo()->whereDate('tasks.due_date', $today);
            }else{
                $tasks->isComplete()->whereDate('tasks.due_date', $today);
            }
//            dd($tasks->toSql());
//            $tasks = $tasks->orderBy('due_date')->count();

            $data[$type] = $tasks->orderBy('due_date')->count();
        }
        return $data;
    }

    public function dashboardTasksList($request)
    {
        $tasks = Task::select('id', 'name', 'note', 'function_id', 'due_date', 'status', 'user_id')
            ->with('event:events.id,events.name')
            ->with('user:id,name')
            ->with('function:id,name')
            ->where('tasks.parent_id', null)
            ->orderBy('due_date');
        if($request->type == "tomorrow") {
            $tasks->whereDate('tasks.due_date', now()->addDay()->format('Y-m-d'));
        }else{
            $tasks->whereDate('tasks.due_date', now()->format('Y-m-d'));
        }
        return $tasks->limit(5)->get();
    }
}