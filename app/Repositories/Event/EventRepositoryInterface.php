<?php

namespace App\Repositories\Event;

interface EventRepositoryInterface
{
    public function getAll($request);
    public function eventJson($request);
    public function create($request);
    public function update($request, $event);
    public function delete($event);
    public function show($event);
    public function favouriteEvent($event);
    public function favourites();
    public function orderEvents($request);
    public function summary();
    public function recentEvents($request);
    public function changeStatus($request);
    public function changePriority($request);
    public function fileUpload($request);
}