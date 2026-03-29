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
            'password' => bcrypt($request->password),
            'community_id' => $request->community_id,
            'floor' => $request->floor,
            'door' => $request->door,
            'role' => 'user',
        ]);

        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if ($user === null) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        if (Hash::check($request->password, $user->password) === false) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }


        $token = $user->createToken('auth_token')->accesToken;

        return response()->json([
            'message' => 'Login correcto',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }
}
