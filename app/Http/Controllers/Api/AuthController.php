<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

AuthController:
/**
 * @group Auth
 */

class AuthController extends Controller
{

    /**
     * Register
     * 
     * Register a new user with the provided information. Returns the created user and an access token.
     * 
     * @unauthenticated
     */

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


    /**
     * Login
     * 
     * Login a user with the provided information. Returns the user and an access token.
     * 
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if ($user === null) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        if (Hash::check($request->password, $user->password) === false) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }


        $token = $user->createToken('auth_token')->accessToken;

        return response()->json([
            'message' => 'Login correcto',
            'data' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }


        /**
     * Logout
     * 
     * Logout the authenticated user by revoking their access token. Returns a success message.
     * 
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'Logout correcto',
        ], 200);
    }
}
