<?php

namespace App\Traits;

trait TaskFilter
{
    public function scopeHasFilter($query, $request, $date=null)
    {
        if (isset($request->name) && trim($request->name) !== '') {
            $this->scopeHasName($query, $request->name);
        }
        if (isset($request->start_date) && trim($request->start_date) !== '') {
            $this->scopeHasStartDate($query, $request->start_date);
        }
        if (isset($request->end_date) && trim($request->end_date) !== '') {
            $this->scopeHasEndDate($query, $request->end_date);
        }
//        if ((isset($request->start_date) || isset($request->end_date)) && (trim($request->start_date) !== '' || trim($request->end_date) !== '')) {
//            $this->scopeHasDateRange($query, $request->start_date, $request->end_date);
//        }
        if (isset($request->function_id) && trim($request->function_id) !== '') {
            $this->scopeHasFunctionId($query, $request->function_id);
        }
        if (isset($request->event_name) && trim($request->event_name) !== '') {
            $this->scopeHasEventName($query, $request->event_name);
        }
        if (isset($request->type) && trim($request->type) !== '') {
            $this->scopeHasType($query, $request->type, $date);
        }
        if (isset($request->status) && trim($request->status) !== '') {
            $this->scopeHasStatus($query, $request->status);
        }
        if (isset($request->orderby) && trim($request->orderby) !== '' && isset($request->order) && trim($request->order) !== '') {
            $this->scopeHasOrder($query, $request->orderby, $request->order);
        }
        return $query;
    }

    public function scopeHasOrder($query, $orderby, $order)
    {
        return $query->orderBy($orderby, $order);
    }

    public function scopeHasName($query, $name)
    {
        $name = trim($name);
        return $query->where('tasks.name', 'like', '%' . $name . '%');
    }

    public function scopeHasType($query, $type, $date)
    {
        $type = trim($type);
        return match ($type) {
            "upcoming" => $this->scopeIsTodo($query),
            "ongoing" => $this->scopeIsOngoing($query, $date),
            "completed" => $this->scopeIsComplete($query),
            default => $query,
        };
    }

    public function scopeIsOngoing($query, $date)
    {
        return $query->where(function ($query) use ($date) {
            $query->where('tasks.status', null)
                ->orWhere('tasks.status', 'incomplete');
            })
            ->whereHas('function', function ($q) use ($date) {
                $q->where('functions.start_date', '<', $date)
                    ->where('functions.end_date', '>', $date);
            });
    }

    public function scopeIsComplete($query)
    {
        return $query->where('tasks.status', 'complete');
    }

    public function scopeIsTodo($query)
    {
        return $query->where(function ($query) {
            $query->where('tasks.status', null)
                ->orWhere('tasks.status', 'incomplete');
        });
    }

    public function scopeHasStatus($query, $status)
    {
        $status = trim($status);
        if($status == 'incomplete'){
            return $query->where(function ($query) {
                $query->where('tasks.status', null)
                    ->orWhere('tasks.status', 'incomplete');
            });
        }else{
            return $query->where('tasks.status', 'complete');
        }
    }

    public function scopeHasStartDate($query, $start_date)
    {
        $start_date = trim($start_date);
        return $query->whereDate('due_date', '>=', $start_date);
    }

    public function scopeHasEndDate($query, $end_date)
    {
        $end_date = trim($end_date);
        return $query->whereDate('due_date', '<=', $end_date);
    }

    public function scopeHasFunctionId($query, $function_id)
    {
        $function_id = trim($function_id);
        return $query->where('function_id', $function_id);
    }

    public function scopeHasEventName($query, $event_name)
    {
        $event_name = trim($event_name);
        return $query->whereHas('event', function ($query) use($event_name) {
            $query->where('events.name', "like", '%'.$event_name.'%');
        });
    }

}