<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $this->createToken($user);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if($this->isInvalidUser($user, $request->password)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $this->createToken($user);

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'token' => $token,
        ], 200);

    }

    public function logout(Request $request)
    {
        $this->revokeCurrentAccessToken($request);

        return response()->json([
            'message' => 'User logged out successfully',
        ], 200);
    }

    private function createToken($user)
    {
        return $user->createToken('auth_token')->plainTextToken;
    }

    private function isInvalidUser(?User $user, string $password)
    {
        return !$user || !Hash::check($password, $user->password);
    }

    private function revokeCurrentAccessToken(Request $request): void
    {
        $request->user()->currentAccessToken()->delete();
    }
}
