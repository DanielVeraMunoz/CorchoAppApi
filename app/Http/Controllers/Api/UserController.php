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
}
