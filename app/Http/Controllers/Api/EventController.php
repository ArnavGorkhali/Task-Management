<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventOrderRequest;
use App\Http\Requests\EventRequest;
use App\Http\Requests\FunctionOrderRequest;
use App\Models\Event;
use App\Models\EventFunction;
use App\Repositories\Event\EventRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * @var EventRepositoryInterface
     */
    private $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $events = $this->eventRepository->getAll($request);
        return success('Events', $events);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param EventRequest $request
     * @return JsonResponse
     */
    public function store(EventRequest $request)
    {
        $response = $this->eventRepository->create($request);
        if($response instanceof Event) {
            return success('Event created successfully.', $response->load('client'));
        }
        return failure('Failed to created event.');
    }

    /**
     * Display the specified resource.
     *
     * @param Event $event
     * @return JsonResponse
     */
    public function show(Event $event)
    {
        $response = $this->eventRepository->show($event);
        return success('Event', $response);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param EventRequest $request
     * @param Event $event
     * @return JsonResponse
     */
    public function update(EventRequest $request, Event $event)
    {
        $response = $this->eventRepository->update($request, $event);
        if($response instanceof Event) {
            return success('Event updated successfully.', $response->load('client'));
        }
        return failure('Failed to updated event.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Event $event
     * @return JsonResponse
     */
    public function destroy(Event $event)
    {
        $event->functions()->delete();
        $response = $this->eventRepository->delete($event);

        if ($response instanceof Event) {
            return success('Event and associated functions deleted successfully.', $response);
        }

        return failure('Failed to delete event.');
    }

    public function eventFiller(Request $request)
    {
        $data = $this->eventRepository->eventJson($request);
        return success(($request->type ? ucfirst($request->type) : 'Events,').' functions and tasks.', $data);
    }

    public function makeFavourite(Event $event)
    {
        $response = $this->eventRepository->favouriteEvent($event);
        if ($response == 'favourited' || $response == 'unfavourited') {
            return success("Event " . $response, Event::findOrFail($event->id));
        }
        return failure($response->getMessage());
    }

    public function favourites(Event $event)
    {
        $response = $this->eventRepository->favourites($event);
        return success("Event favourites", $response);
    }

    public function order(EventOrderRequest $request)
    {
        $response = $this->eventRepository->orderEvents($request);
        if($response instanceof Event) {
            return success('Events ordered successfully');
        }
        return failure('Events failed to order.');
    }

    public function summary()
    {
        $response = $this->eventRepository->summary();
        return success('Events summary', $response);
    }

    public function recentEvents(Request $request)
    {
        $response = $this->eventRepository->recentEvents($request);
        return success('Recent events', $response);
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'event_id' => 'required|int|exists:events,id',
            'status' => 'required|string|'.Rule::in(config('options.event_status'))
        ]);
        $response = $this->eventRepository->changeStatus($request);
        if($response instanceof Event) {
            return success('Event '.$request->status, $response);
        }
        return failure('Failed to change status');
    }

    public function changePriority(Request $request)
    {
        $request->validate([
            'event_id' => 'required|int|exists:events,id',
            'priority' => 'required|string|'.Rule::in(config('options.event_priority'))
        ]);
        $response = $this->eventRepository->changePriority($request);
        if($response instanceof Event) {
            return success('Event priority set to '.$request->priority, $response);
        }
        return failure('Failed to change status');
    }

    public function fileUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5000',
        ], ['max' => 'The :attribute most not be greater than 5MB.']);
        $response = $this->eventRepository->fileUpload($request);
        if($response) {
            return success("File uploaded.", $response);
        }
        return failure('Failed to change status');
    }
}
