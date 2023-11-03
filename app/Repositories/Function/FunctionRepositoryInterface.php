<?php

namespace App\Repositories\Function;

interface FunctionRepositoryInterface
{
    public function getAll();
    public function create($request);
    public function update($request, $function);
    public function delete($function);
    public function show($function);
    public function orderFunctions($request);
    public function changeStatus($request);
    public function changePriority($request);
}