<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\Vendor;
use App\Repositories\Vendor\VendorRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VendorController extends Controller
{
    /**
     * @var VendorRepositoryInterface
     */
    private $vendorRepository;

    public function __construct(VendorRepositoryInterface $vendorRepository)
    {
        $this->vendorRepository = $vendorRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $vendors = $this->vendorRepository->getAll($request);
        return success('Vendors', $vendors);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VendorRequest $request
     * @return JsonResponse
     */
    public function store(VendorRequest $request)
    {
        $response = $this->vendorRepository->create($request);
        if($response instanceof Vendor) {
            return success('Vendor created successfully.', $response);
        }
        return failure('Failed to created vendor.');
    }

    /**
     * Display the specified resource.
     *
     * @param Vendor $vendor
     * @return JsonResponse
     */
    public function show(Vendor $vendor)
    {
        $vendor = Vendor::withCount('tasks', 'complete_tasks', 'incomplete_tasks')->find($vendor->id);
        return success('Vendor', $vendor);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param VendorRequest $request
     * @param Vendor $vendor
     * @return JsonResponse
     */
    public function update(VendorRequest $request, Vendor $vendor)
    {
        $response = $this->vendorRepository->update($request, $vendor);
        if($response instanceof Vendor) {
            return success('Vendor updated successfully.', $response);
        }
        return failure('Failed to updated vendor.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Vendor $vendor
     * @return JsonResponse
     */
    public function destroy(Vendor $vendor)
    {
        $response = $this->vendorRepository->delete($vendor);
        if($response instanceof Vendor) {
            return success('Vendor deleted successfully.', $response);
        }
        return failure('Failed to deleted vendor.');
    }

    public function tasks(Request $request, Vendor $vendor)
    {
        $response = $this->vendorRepository->vendorTasks($request, $vendor);
        return success('Vendor tasks.', $response);
    }
}
