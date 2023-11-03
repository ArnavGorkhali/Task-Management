<?php

namespace App\Repositories\Role;

interface RoleRepositoryInterface
{
    public function getRole();

    public function update($request);

    public function create($request);
}
