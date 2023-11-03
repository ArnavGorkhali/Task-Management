<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Event;
use App\Models\EventFunction;
use App\Models\Task;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\UserResource;
use Spatie\Permission\Models\Permission;


class AuthController extends Controller
{

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * login api
     *
     * @return JsonResponse
     */
    public function login()
    {
        $this->validate(request(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $roleName = $user->getRoleNames()->first(); 
    
            // Get the user's permission names
            $permissions = Permission::whereHas('roles', function ($query) use ($user) {
                $query->whereIn('roles.id', $user->roles->pluck('id'));
            })->pluck('name');
            return success('User token generated', ['token'=>$user->createToken('Lami')->accessToken, 'role'=> $roleName,'permissions'=> $permissions]);
        } else {
            return failure(' These credentials do not match our records.',400);
        }
    }

    /**
     * Register api
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $user = $this->userRepository->store($request);
        event(new Registered($user));
        $success['token'] = $user->createToken('Lami')->accessToken;
        return success('User created and token generated',['token'=>$success['token'],'user'=>$user]);
    }

    public function logout()
    {
        if (Auth::check()) {
            if(Auth::user()->tokens()->delete()){
                return success('User logged out.');
            }
            return failure('Failed to logout.');
        }
    }

    public function profile()
    {
        return response()->json([
            'user' => new UserResource(auth()->user())
        ]);
    }

    public function changeStatusPriority()
    {
        $events = DB::table('events')->update(['status' => null, 'priority' => 'low']);
        $eventFunctions = DB::table('functions')->update(['status' => null, 'priority' => 'low']);
        $eventFunctions = DB::table('tasks')->update(['status' => null, 'priority' => 'low']);
        return success('done');
    }
}
