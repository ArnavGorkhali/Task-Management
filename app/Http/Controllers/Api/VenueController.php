<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VenueRequest;
use App\Models\Venue;
use App\Repositories\Venue\VenueRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VenueController extends Controller
{
    /**
     * @var VenueRepositoryInterface
     */
    private $venueRepository;

    public function __construct(VenueRepositoryInterface $venueRepository)
    {
        $this->venueRepository = $venueRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $venues = $this->venueRepository->getAll($request);
        return success('Venues', $venues);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VenueRequest $request
     * @return JsonResponse
     */
    public function store(VenueRequest $request)
    {
        $response = $this->venueRepository->create($request);
        if($response instanceof Venue) {
            return success('Venue created successfully.', $response);
        }
        return failure('Failed to created venue.');
    }

    /**
     * Display the specified resource.
     *
     * @param Venue $venue
     * @return JsonResponse
     */
    public function show(Venue $venue)
    {
        return success('Venue', $venue);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param VenueRequest $request
     * @param Venue $venue
     * @return JsonResponse
     */
    public function update(VenueRequest $request, Venue $venue)
    {
        $response = $this->venueRepository->update($request, $venue);
        if($response instanceof Venue) {
            return success('Venue updated successfully.', $response);
        }
        return failure('Failed to updated venue.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Venue $venue
     * @return JsonResponse
     */
    public function destroy(Venue $venue)
    {
        $response = $this->venueRepository->delete($venue);
        if($response instanceof Venue) {
            return success('Venue deleted successfully.', $response);
        }
        return failure('Failed to deleted venue.');
    }

    public function venueFiller($type)
    {
        $data = $this->venueRepository->venueJson($type);
        return success(ucfirst($type).' functions and tasks.', $data);
    }
}
