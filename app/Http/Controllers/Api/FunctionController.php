<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FunctionOrderRequest;
use App\Http\Requests\FunctionRequest;
use App\Models\Event;
use App\Models\EventFunction;
use App\Repositories\Function\FunctionRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

use GuzzleHttp\Client;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use Brevo\Client\Configuration;
use Dotenv\Dotenv;


class FunctionController extends Controller
{
    /**
     * @var FunctionRepositoryInterface
     */
    private $functionRepository;

    public function __construct(FunctionRepositoryInterface $functionRepository)
    {
        $this->functionRepository = $functionRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $functions = $this->functionRepository->getAll();
        return success('Functions', $functions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FunctionRequest $request
     * @return JsonResponse
     */
    public function store(FunctionRequest $request)
    {
        $response = $this->functionRepository->create($request);
        if($response instanceof EventFunction) {
            return success('Function created successfully.', $response);
        }
        return failure('Failed to created function.');
    }

    /**
     * Display the specified resource.
     *
     * @param EventFunction $function
     * @return JsonResponse
     */
    public function show(EventFunction $function)
    {
        $response = $this->functionRepository->show($function);
        return success('Function', $response);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param FunctionRequest $request
     * @param EventFunction $function
     * @return JsonResponse
     */
    public function update(FunctionRequest $request, EventFunction $function)
    {
        $response = $this->functionRepository->update($request, $function);
        if($response instanceof EventFunction) {
            return success('Function updated successfully.', $response);
        }
        return failure('Failed to updated function.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param EventFunction $function
     * @return JsonResponse
     */
    public function destroy(EventFunction $function)
    {
        $response = $this->functionRepository->delete($function);
        if($response instanceof EventFunction) {
            return success('Function deleted successfully.', $response);
        }
        return failure('Failed to deleted function.');
    }

    public function deleteFunctionByEID($event)
    {
        $event->functions()->delete();
        $response = $this->eventRepository->delete($event);

        if ($response instanceof Event) {
            return success('Event and associated functions deleted successfully.', $response);
        }

        return failure('Failed to delete event.');
    }

    public function order(FunctionOrderRequest $request)
    {
        $response = $this->functionRepository->orderFunctions($request);
        if($response instanceof EventFunction) {
            return success('Functions ordered successfully');
        }
        return failure('Functions failed to order.');
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'function_id' => 'required|int|exists:functions,id',
            'status' => 'required|string|'.Rule::in(config('options.event_status'))
        ]);
        $response = $this->functionRepository->changeStatus($request);
        if($response instanceof EventFunction) {
            return success('Function '.$request->status, $response);
        }
        return failure('Failed to change status');
    }

    public function changePriority(Request $request)
    {
        $request->validate([
            'function_id' => 'required|int|exists:functions,id',
            'priority' => 'required|string|'.Rule::in(config('options.event_priority'))
        ]);
        $response = $this->functionRepository->changePriority($request);
        if($response instanceof EventFunction) {
            return success('Function priority set to '.$request->priority, $response);
        }
        return failure('Failed to change status');
    }

    public function showByName()
    {
        $functions = EventFunction::select('functions.id as id', 'functions.name', 'clients.name as client_name', 'clients.email as email', 'functions.end_date', 'functions.status')
        ->join('events', 'functions.event_id', '=', 'events.id')
        ->join('clients', 'events.client_id', '=', 'clients.id')
        ->where('functions.name', "wedding")
        ->where('events.deleted_at', null)
        ->where('functions.deleted_at', null)
        ->where('clients.deleted_at', null)
        ->where('events.status', 'complete')
        ->get();
        
        return response()->json($functions);
    }

    public function sendEmail(Request $request)
    {
        // Load environment variables from .env
        $dotenv = Dotenv::createImmutable(base_path());
        $dotenv->load();

        $emailList = $request->input('emails');

        $template = $request->input('template');
        $subject = $request->input('heading');

        // Get the SendinBlue API key from the .env file
        $apiKey = $_ENV['BREVO_API_KEY'];

        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
        
        $apiInstance = new TransactionalEmailsApi(new Client(), $config);
        
        foreach ($emailList as $email) {
            $sendSmtpEmail = new SendSmtpEmail([
                'subject' => $subject,
                'sender' => ['name' => 'Clover Team', 'email' => 'Cloverevents1998@gmail.com'],
                'replyTo' => ['name' => 'Clover Team', 'email' => 'Cloverevents1998@gmail.com'],
                'to' => [['email' => $email]],
                'templateId' => $template,
                'params' => ['bodyMessage' => 'made just for you!']
            ]);
            try {
                $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
                return response()->json($result);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }
}
