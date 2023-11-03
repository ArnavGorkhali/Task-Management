<?php

namespace App\Traits;

trait VendorFilter
{
    public function scopeHasFilter($query, $request)
    {
        if (isset($request->name) && trim($request->name) !== '') {
            $this->scopeHasName($query, $request->name);
        }
        if (isset($request->mobile) && trim($request->mobile) !== '') {
            $this->scopeHasMobile($query, $request->mobile);
        }
        if (isset($request->category) && trim($request->category) !== '') {
            $this->scopeHasCategory($query, $request->category);
        }
        if (isset($request->orderby) && trim($request->orderby) !== '' && isset($request->order) && trim($request->order) !== '') {
            $this->scopeHasOrder($query, $request->orderby, $request->order);
        }
        return $query;
    }

    public function scopeHasName($query, $name)
    {
        $name = trim($name);
        return $query->where('name', 'like', '%'.$name.'%');
    }

    public function scopeHasOrder($query, $orderby, $order)
    {
        return $query->orderBy($orderby, $order);
    }

    public function scopeHasMobile($query, $mobile)
    {
        $mobile = trim($mobile);
        return $query->where('mobile', 'like', '%'.$mobile.'%');
    }

    public function scopeHasCategory($query, $category)
    {
        $category = trim($category);
        return $query->where('category', $category);
    }
}