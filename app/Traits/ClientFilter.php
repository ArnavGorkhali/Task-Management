<?php

namespace App\Traits;

trait ClientFilter
{
    public function scopeHasFilter($query, $request)
    {
        if (isset($request->mobile) && trim($request->mobile) !== '') {
            $this->scopeHasMobile($query, $request->mobile);
        }
        if (isset($request->search) && trim($request->search) !== '') {
            $this->scopeHasSearch($query, $request->search);
        }
        return $query;
    }

    public function scopeHasMobile($query, $mobile)
    {
        $mobile = trim($mobile);
        return $query->where('mobile', 'like', '%'.$mobile.'%');
    }

    public function scopeHasSearch($query, $search)
    {
        $search = trim($search);
        return $query->where(function($q)use($search){
            $q->where('mobile', 'like', '%'.$search.'%')
                ->orWhere('name', 'like', '%'.$search.'%');
        });
    }
}