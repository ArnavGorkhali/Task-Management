<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Repositories\Client\ClientRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends Controller
{
    /**
     * @var ClientRepositoryInterface
     */
    private $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $clients = $this->clientRepository->getAll($request);
        return success('Clients', $clients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ClientRequest $request
     * @return JsonResponse
     */
    public function store(ClientRequest $request)
    {
        $response = $this->clientRepository->create($request);
        if($response instanceof Client) {
            return success('Client created successfully.', $response);
        }
        return failure('Failed to created client.');
    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     * @return JsonResponse
     */
    public function show(Client $client)
    {
        return success('Client', $client);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param ClientRequest $request
     * @param Client $client
     * @return JsonResponse
     */
    public function update(ClientRequest $request, Client $client)
    {
        $response = $this->clientRepository->update($request, $client);
        if($response instanceof Client) {
            return success('Client updated successfully.', $response);
        }
        return failure('Failed to updated client.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Client $client
     * @return JsonResponse
     */
    public function destroy(Client $client)
    {
        $response = $this->clientRepository->delete($client);
        if($response instanceof Client) {
            return success('Client deleted successfully.', $response);
        }
        return failure('Failed to deleted client.');
    }

    public function clientFiller($type)
    {
        $data = $this->clientRepository->clientJson($type);
        return success(ucfirst($type).' functions and tasks.', $data);
    }
}
