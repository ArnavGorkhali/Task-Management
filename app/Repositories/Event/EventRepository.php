<?php

namespace App\Repositories\Event;

use App\Models\Client;
use App\Models\Event;
use App\Models\EventFunction;
use App\Models\Task;
use App\Repositories\Client\ClientRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventRepository implements EventRepositoryInterface
{

    public function getAll($request)
    {
        return Event::hasFilter($request)->with('client')->latest()->paginate($request->per_page?:15);
    }

    public function eventJson($request)
    {
        return $request->type ? (config('events.' . $request->type) ?: ['function' => []]) : config('events');
    }

    public function create($request)
    {
        DB::beginTransaction();
//        $client_mobile = $request->mobile_number;
//        if (!Client::where('mobile', $client_mobile)->first()) {
//            $client_data = new \Illuminate\Http\Request([
//                'name' => $request->client_name,
//                'mobile' => $request->mobile_number,
//                'company_name' => $request->company_name,
//                'email' => $request->email,
//                'address' => $request->address,
//            ]);
//            (new ClientRepository())->create($client_data);
//        }
        $event = Event::create($request->input());
        if ($eventdata = config('events.' . $request->category)) {
            if (isset($eventdata['function']) && $eventdata['function']) {
                foreach ($eventdata['function'] as $function) {
                    $eventfunction = EventFunction::create([
                        'name' => $function['name'],
                        'event_id' => $event->id
                    ]);
                    if (isset($function['tasks']) && $function['tasks']) {
                        foreach ($function['tasks'] as $task) {
                            $event_task = Task::create([
                                'name' => $task['name'],
                                'function_id' => $eventfunction->id
                            ]);
                            if (isset($task['subtasks']) && $task['subtasks']) {
                                foreach ($task['subtasks'] as $subtask) {
                                    $event_subtask = Task::create([
                                        'name' => $subtask['name'],
                                        'parent_id' => $event_task->id
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
        DB::commit();
        return $event;
    }

    public function show($event)
    {
        $event = Event::with('functions.tasks.subtasks')
            ->with('functions.tasks.vendor:id,name', 'client')
            ->findOrFail($event->id);
        return $event;
    }

    public function update($request, $event)
    {
//        $client_mobile = $request->mobile_number;
//        if (!Client::where('mobile', $client_mobile)->first()) {
//            $client_data = new \Illuminate\Http\Request([
//                'name' => $request->client_name,
//                'mobile' => $request->mobile_number,
//                'company_name' => $request->company_name,
//                'email' => $request->email,
//                'address' => $request->address,
//            ]);
//            (new ClientRepository())->create($client_data);
//        }
        $event->update($request->input());
        return $event;
    }

    public function delete($event)
    {
        $event->delete();
        return $event;
    }

    public function favouriteEvent($event)
    {
        $auth_user = Auth::user();
        DB::beginTransaction();
        try {
            $maxOrder = Event::withoutGlobalScopes()->where('id', '!=', $event->id)->orderBy('order', 'desc')->first();
            if($maxOrder){
                $event->order = $maxOrder->order+1;
                $event->save();
            }
            if ($event->is_favourite) {
                $auth_user->favourites()->detach($event->id);
                $favourite_status = "unfavourited";
            } else {
                $auth_user->favourites()->attach($event->id);
                $favourite_status = "favourited";
            }
            DB::commit();
            return $favourite_status;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function favourites()
    {
        $auth_user = Auth::user();
        return $auth_user->favourites()
            ->where(function($q){
                $q->where('status', 'incomplete')
                    ->orWhere('status', null);
            })
            ->orderBy('order', 'asc')->paginate();
    }

    public function orderEvents($request)
    {
        DB::beginTransaction();
        try {
            foreach ($request->events as $key => $event) {
                $eventDB = Event::whereId($event['id'])
                    ->first();
                if ($eventDB) {
                    $eventDB->order = $event['order'];
                    $eventDB->save();
                }
            }
            DB::commit();
            return $eventDB;
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
        }
    }

    public function summary()
    {
        $upcoming_events = Event::isTodo()->count();
        $ongoing_events = Event::isOngoing()->count();
        $complete_events = Event::isComplete()->count();
        $total_events = Event::count();

        return [
            'upcoming' => $upcoming_events,
            'ongoing' => $ongoing_events,
            'complete' => $complete_events,
            'total' => $total_events
        ];
    }

    public function recentEvents($request)
    {
        return Event::where('end_date', '>', now()->format('Y-m-d H:i:s'))
            ->with('functions')
            ->orderBy('start_date', 'asc')
            ->paginate($request->per_page?:15);
    }

    public function changeStatus($request)
    {
        $event = Event::find($request->event_id);
        $event->status = $request->status;
        $event->save();
        return $event;
    }

    public function changePriority($request)
    {
        $event = Event::find($request->event_id);
        $event->priority = $request->priority;
        $event->save();
        return $event;
    }

  

    public function fileUpload($request)
    {
        try {
            $now = Carbon::now();
            $path = '/uploads/content/' . $now->year . "/" . $now->month;
            $filePath = Storage::disk('public')->put($path, $request->file, 'public');
            return env('APP_URL') . Storage::url($filePath);
        } catch (\Exception $e) {
            return false;
        }

    }
}