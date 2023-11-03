<?php

namespace App\Repositories\Function;

use App\Models\Event;
use App\Models\EventFunction;
use App\Repositories\Function\FunctionRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FunctionRepository implements FunctionRepositoryInterface
{

    public function getAll()
    {
        return EventFunction::get();
    }

    public function create($request)
    {
        return EventFunction::create($request->input());
    }

    public function show($function)
    {
        $function = EventFunction::withCount('tasks', 'complete_tasks', 'vendors')
            ->with('tasks.subtasks', 'venue')->findOrFail($function->id);
        return $function;
    }

    public function update($request, $function)
    {
        $function->update($request->input());
        return $function;
    }

    public function delete($function)
    {
        $function->delete();
        return $function;
    }

    public function orderFunctions($request)
    {
        DB::beginTransaction();
        try{
            foreach($request->functions as $key=>$function){
                $eventFunction = EventFunction::whereId($function['id'])
                    ->first();
                if($eventFunction){
                    $eventFunction->order = $function['order'];
                    $eventFunction->save();
                }
            }
            DB::commit();
            return $eventFunction;
        }catch(\Exception $e){
            DB::rollBack();
            return $e;
        }
    }

    public function changeStatus($request)
    {
        DB::beginTransaction();
        $eventFunction = EventFunction::find($request->function_id);
        $eventFunction->status = $request->status;
        $eventFunction->save();
        DB::commit();
        return $eventFunction;
    }

    public function updateParentStatus($eventFunction)
    {
        $event = $eventFunction->event;
        if($event){
            $eventFunctions = $event->functions()->where(function ($q){
                $q->where('status', 'incomplete')
                    ->orWhere('status', null);
            })->count();
            if($eventFunctions == 0) {
                $event->status = "complete";
                $event->save();
            } else {
                $event->status = "incomplete";
                $event->save();
            }
        }
    }

    public function myCalender($request)
    {
        $year = $request->year ?: now()->year;
        $month = $request->month ?: now()->month;
        $events = EventFunction::select('id', 'name', 'status', 'start_date', 'end_date', 'color_code','event_id')
            ->with('event:id,name')
            ->where(function($q) use($year, $month){
            $q->where(function($query) use($year, $month) {
                    $query->whereYear('start_date', $year)
                        ->whereMonth('start_date', $month);
                })->orWhere(function($query) use($year, $month) {
                    $query->whereYear('end_date', $year)
                        ->whereMonth('end_date', $month);
                });
            })
            ->get();
        return $events;
     
    }

    public function changePriority($request)
    {
        $eventFunction = EventFunction::find($request->function_id);
        $eventFunction->priority = $request->priority;
        $eventFunction->save();
        return $eventFunction;
    }
}