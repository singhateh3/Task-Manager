<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\Usercollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(RegisterUserRequest $request)
    {

        $validated = $request->validated();
        $user = User::create($validated);
        // Auth::login($user);
        return response()->json(['message' => 'User registered successfully', 'user' => new UserResource($user)]);
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where(['email' => $credentials['email']])->first();


        if (!$user && !Hash::check($credentials['password'], $user->password)) {
            return response()->json('Validation Error');
        }
        $token = $user->createToken('api_token');

        return response()->json(['message' => 'user login successful', 'token' => $token->plainTextToken, 'user' => new UserResource($user)]);
    }

    public function index()
    {
        $this->authorize('admin-action');
        $users = User::all();
        return response()->json(['message' => 'all users', 'users' => new Usercollection($users)]);
    }
}
