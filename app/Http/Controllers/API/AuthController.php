<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "validation error",
                "data" => $validator->errors()->all()
            ], 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        
        $response = [];

        $response["token"] = $user->createToken("mak-travels")->accessToken;
        $response["name"] = $user->name;
        $response["email"] = $user->email;

        return response()->json([
            "status" => 1,
            "message" => "User Registered Successfully",
            "data" => $response
        ], 200);
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => "Validation error",
                "errors" => $validator->errors()
            ], 422);
        }
    
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            
            // Revoke all existing tokens for this user
            $user->tokens()->delete();
            
            $response = [
                "token" => $user->createToken("mak-travels")->accessToken,
                "name" => $user->name,
                "email" => $user->email
            ];
    
            return response()->json([
                "status" => 1,
                "message" => "User logged in successfully",
                "data" => $response
            ], 200);
        }
    
        return response()->json([
            "status" => 0,
            "message" => "Invalid credentials",
            "data" => null
        ], 401);
    }

    public function logout(Request $request) {
        $request->user()->token()->delete();
        return response()->json([
            'status' => 1,
            'message' => 'Successfully logged out',
        ], 200);
    }

}