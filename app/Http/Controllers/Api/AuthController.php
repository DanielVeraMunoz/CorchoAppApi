<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use App\Models\User;


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



}
