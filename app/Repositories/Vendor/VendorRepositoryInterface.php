<?php

namespace App\Repositories\Vendor;

interface VendorRepositoryInterface
{
    public function getAll($request);
    public function create($request);
    public function update($request, $vendor);
    public function delete($vendor);
    public function vendorTasks($request, $vendor);
}