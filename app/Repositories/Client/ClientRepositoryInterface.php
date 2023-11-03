<?php

namespace App\Repositories\Client;

interface ClientRepositoryInterface
{
    public function getAll($request);
    public function create($request);
    public function update($request, $client);
    public function delete($client);
}