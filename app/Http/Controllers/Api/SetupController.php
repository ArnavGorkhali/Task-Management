<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function index()
    {
        $event_categories = array_keys(config('events'));
        $vendor_categories = config('options.vendor_categories');
        $data = [
            'categories' => $event_categories,
            'vendor_categories' => $vendor_categories,
        ];
        return success('Setup', $data);
    }
}
