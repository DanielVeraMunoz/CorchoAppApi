<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request){
        $users = \App\Models\User::all();

        return response()->json([
            'message' => 'Usuarios obtenidos correctamente',
            'data' => $users,
        ], 200);
    }

    public function show(Request $request, $id){
        $user = \App\Models\User::find($id);

        if (!$user){
            return response()->json([
                'message' => 'Usuario no encontrado'],
                404);
        }

        return response()->json([
            'message' => 'Usuario obtenido correctamente',
            'data' => $user,
        ], 200);

    }

    
}
