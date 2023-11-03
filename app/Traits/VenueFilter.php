<?php

namespace App\Traits;

trait VenueFilter
{
    public function scopeHasFilter($query, $request)
    {
        if (isset($request->name) && trim($request->name) !== '') {
            $this->scopeHasName($query, $request->name);
        }
        return $query;
    }

    public function scopeHasName($query, $name)
    {
        $name = trim($name);
        return $query->where('name', 'like', '%'.$name.'%');
    }
}