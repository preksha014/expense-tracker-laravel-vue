<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login(LoginRequest $request)
    {
        try{
            $validated = $request->only('email', 'password');

        if (Auth::attempt($validated)) {
            $user = Auth::user();
            $token = $user->createToken('main')->plainTextToken;

            return ApiResponse::success([
                'user' => $user,
                'token' => $token
            ]);
        }
        }catch(\Exception $e){
            return ApiResponse::error('Something went wrong: ' . $e->getMessage());
        }
        

    }

    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create($validated);

        $token = $user->createToken('main')->plainTextToken;

        return ApiResponse::success(data: [
            'user' => $user,
            'token' => $token
        ]);
    }
    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->currentAccessToken()->delete();
        return ApiResponse::success([], 'Logged Out Successfully');
    }
}
