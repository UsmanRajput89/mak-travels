<?php

namespace App\Http\Controllers\API;

use App\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Log;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $userDTO = new UserDTO($request->username, $request->email, $request->password);

        $user = $this->authService->register($userDTO);

        $response = [
            "token" => $user->createToken("mak-travels")->accessToken,
            "username" => $user->username,
            "email" => $user->email,
        ];

        return response()->json([
            "status" => 1,
            "message" => "User Registered Successfully",
            "data" => $response,
        ], 200);
    }

    public function login(LoginRequest $request)
    {
        $user = $this->authService->login($request->email, $request->password);

        if ($user) {
            // Revoke all existing tokens for this user
            $user->tokens()->delete();

            $response = [
                "token" => $user->createToken("mak-travels")->accessToken,
                "username" => $user->username,
                "email" => $user->email,
            ];

            return response()->json([
                "status" => 1,
                "message" => "User logged in successfully",
                "data" => $response,
            ], 200);
        }

        return response()->json([
            "status" => 0,
            "message" => "Invalid credentials",
            "data" => null,
        ], 401);
    }

    public function logout(Request $request)
    {
        $response = $this->authService->logout();

        return response()->json([
            'status' => 1,
            'message' => 'Successfully logged out',
        ], 200);
    }
}