<?php 
namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class AuthService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register($data)
    {
        $user = $this->userRepository->create([
            'username' => $data->username,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        return $user;
    }

    public function login($email, $password)
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }
    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            // Revoke all tokens for the current user
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return [
                'status' => 1,
                'message' => 'Successfully logged out.',
            ];
        }

        return [
            'status' => 0,
            'message' => 'No authenticated user found.',
        ];
    }
}
