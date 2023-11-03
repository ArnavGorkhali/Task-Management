<?php

namespace App\Repositories\Client;

use App\Models\Client;

class ClientRepository implements ClientRepositoryInterface
{

    public function getAll($request)
    {
        return Client::hasFilter($request)->paginate($request->per_page?:15);
    }

    public function create($request)
    {
        return Client::create($request->input());
    }

    public function update($request, $client)
    {
        $client->update($request->input());
        return $client;
    }

    public function delete($client)
    {
        $client->delete();
        return $client;
    }
}