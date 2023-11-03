<?php

namespace App\Repositories\Role;

use App\Services\FileUploadService;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RoleRepository implements RoleRepositoryInterface
{
    public function getRole()
    {
        $role = User::select('id', 'name', 'profile_pic','phone', 'email')
        ->with('roles:roles.id, roles.name')
        ->with('permissions:permissions.id, permissions.name');

        $data = [$role];
        return $data;
    }

    public function create($request)
    {
       

    }

    public function update($request)
    {
        
    }
    
}
