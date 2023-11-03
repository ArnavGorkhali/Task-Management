<?php

namespace App\Repositories\Task;

interface TaskRepositoryInterface
{
    public function getAll();
    public function create($request);
    public function update($request, $task);
    public function delete($task);
    public function orderTasks($request);
}