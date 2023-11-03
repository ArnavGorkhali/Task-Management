<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
// use App\Repositories\Event\EventRepository;
use App\Repositories\Task\TaskRepository;
use App\Repositories\Function\FunctionRepository;
use Illuminate\Http\Request;

class MydayController extends Controller
{
    public function worklist(Request $request)
    {
        $worklist = (new TaskRepository())->getWorkList($request);
//        dd($worklist);
        return success("My day work list.", $worklist);
    }

    public function myCalender(Request $request)
    {
        $eventlist = (new FunctionRepository())->myCalender($request);
        return success("My day calender view.", $eventlist);
    }
}
