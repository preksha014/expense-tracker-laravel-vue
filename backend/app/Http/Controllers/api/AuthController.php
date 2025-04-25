<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function login(Request $request){
        $user=User::where('email',$request->email)->first();
        if (!$user || !password_verify($request->password, $user->password)) {
            return ApiResponse::error('Invalid Credentials');
        }
        $token=$user->createToken('main')->plainTextToken;
        return ApiResponse::success([
            'user'=>$user,
            'token'=>$token
        ]);
    }

    public function register(Request $request){
        $validated=$request->validate([
            'name'=>'required|max:255',
            'email'=>'required|email|unique:users',
            'password'=>'required'
        ]);
        $user=User::create($validated);
        $token=$user->createToken('main')->plainTextToken;
        return ApiResponse::success(data: [
            'user'=>$user,
            'token'=>$token
        ]);
    }
    public function logout(Request $request){
        $user=Auth::user();
        $user->currentAccessToken()->delete();
        return ApiResponse::success([],'Logged Out Successfully');
    }
}
