<?php

namespace App\Repositories\Venue;

use App\Models\Venue;
use App\Repositories\Venue\VenueRepositoryInterface;

class VenueRepository implements VenueRepositoryInterface
{

    public function getAll($request)
    {
        return Venue::hasFilter($request)->paginate();
    }

    public function create($request)
    {
        return Venue::create($request->input());
    }

    public function update($request, $venue)
    {
        $venue->update($request->input());
        return $venue;
    }

    public function delete($venue)
    {
        $venue->delete();
        return $venue;
    }
}