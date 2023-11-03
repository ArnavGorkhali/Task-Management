<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Repositories\Role;
use App\Models\User;
use App\Repositories\Profile\ProfileRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class ProfileController extends Controller
{
    /**
     * @var ProfileRepositoryInterface
     */
    private $profileRepository;

    public function __construct(ProfileRepositoryInterface $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        if($user->isSocialUser() && empty($user->password))
        {
            $this->validate($request, [
                'new_password' => 'required|min:8|confirmed',
            ]);
        }else{
            $this->validate($request, [
                'current_password' => 'required|min:8',
                'new_password' => 'required|min:8|confirmed',
            ]);
        }
        $response = $this->profileRepository->changePassword($request);
        if ($response instanceof User) {
            return success('Password changed successfully.');
        }
        if ($response instanceof ValidationException) {
            return failure($response->getMessage(), 422, ['errors' => $response->errors()]);
        }
        return failure($response->getMessage());

    }

    public function getUser()
    { 
        return success('User.', Auth::user());
    }

    public function getPermission(){
        $user = Auth::user();
    
        $permissions = Permission::whereHas('roles', function ($query) use ($user) {
            $query->whereIn('roles.id', $user->roles->pluck('id'));
        })->pluck('name');
    
        return success('Permission',$permissions);
    }

    public function update(ProfileRequest $request)
    {
        $response = $this->profileRepository->update($request);
        if($response instanceof User){
            return success('Profile Updated successfully.', $response);
        }
        return failure('Failed to update profile.');
    }
}
