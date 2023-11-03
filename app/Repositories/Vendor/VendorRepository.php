<?php

namespace App\Repositories\Vendor;

use App\Models\Task;
use App\Models\Vendor;

class VendorRepository implements VendorRepositoryInterface
{

    public function getAll($request)
    {
        return Vendor::hasFilter($request)->latest()->paginate($request->per_page?:15);
    }

    public function create($request)
    {
        return Vendor::create($request->input());
    }

    public function update($request, $vendor)
    {
        $vendor->update($request->input());
        return $vendor;
    }

    public function delete($vendor)
    {
        $vendor->delete();
        return $vendor;
    }

    public function vendorTasks($request, $vendor)
    {
        $tasks = Task::where('vendor_id', $vendor->id)
            ->hasFilter($request, $request->start_date?:now()->format('Y-m-d H:i:s'))
            ->where('parent_id', null)
            ->with('function:id,name,venue_id', 'function.venue')
            ->with('event:events.id,events.name')
            ->latest()
            ->paginate($request->per_page?:15);
        return $tasks;
    }
}