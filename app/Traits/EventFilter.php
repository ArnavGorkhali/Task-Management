<?php

namespace App\Traits;

trait EventFilter
{
    public function scopeHasFilter($query, $request)
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
        if (isset($request->client_id) && trim($request->client_id) !== '') {
            $this->scopeHasClientId($query, $request->client_id);
        }
        if (isset($request->type) && trim($request->type) !== '') {
            $this->scopeHasType($query, $request->type);
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
        return $query->where('name', 'like', '%' . $name . '%');
    }

    public function scopeHasStartDate($query, $start_date)
    {
        $start_date = trim($start_date);
        return $query->whereDate('start_date', '>=', $start_date);
    }

    public function scopeHasEndDate($query, $end_date)
    {
        $end_date = trim($end_date);
        return $query->whereDate('end_date', '<=', $end_date);
    }

    public function scopeHasClientId($query, $client_id)
    {
        $client_id = trim($client_id);
        return $query->where('client_id', $client_id);
    }

    public function scopeHasType($query, $type)
    {
        $type = trim($type);
        return match ($type) {
            "upcoming" => $this->scopeIsTodo($query),
            "ongoing" => $this->scopeIsOngoing($query),
            "completed" => $this->scopeIsComplete($query),
            default => $query,
        };
    }

    public function scopeIsOngoing($query)
    {
        return $query->where(function ($query) {
            $query->where('events.status', null)
                ->orWhere('events.status', 'incomplete');
        })->whereDate('events.start_date', '<=', now())
            ->whereDate('events.end_date', '>=', now());

    }

    public function scopeIsComplete($query)
    {
        return $query->where(function ($query) {
            $query->where('events.status', 'complete')
                ->orWhereDate('events.end_date', '<', now());
        });
    }

    public function scopeIsTodo($query)
    {
        return $query->where(function ($query) {
            $query->where('events.status', null)
                ->orWhere('events.status', 'incomplete');
        })->whereDate('events.start_date', '>', now());
    }
}