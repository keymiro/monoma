<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $request->validated();

        $credentials = $request->only('username', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'meta' => [
                        'success' => false,
                        'errors' => ['Credenciales invalidas']
                    ]
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'meta' => [
                    'success' => false,
                    'errors' => ['No se pudo crear el token']
                ]
            ], 500);
        }

        return response()->json([
            'meta' => [
                'success' => true,
                'errors' => []
            ],
            'data' => [
                'token' => $token,
                'minutes_to_expire' => config('jwt.ttl') / 60
            ]
        ], 200);

    }

    public function logout(): JsonResponse
    {
        Auth::logout();

        return response()->json(['message' => 'Haz salido correctamente']);
    }
}
