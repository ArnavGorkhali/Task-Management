<?php

namespace App\Repositories\Venue;

interface VenueRepositoryInterface
{
    public function getAll($request);
    public function create($request);
    public function update($request, $venue);
    public function delete($venue);
}