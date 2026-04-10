<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = \App\Models\User::all();

        return response()->json([
            'message' => 'Usuarios obtenidos correctamente',
            'data' => $users,
        ], 200);
    }

    public function show(Request $request, $id)
    {
        $user = \App\Models\User::find($id);

        if (!$user) {
            return response()->json(
                [
                    'message' => 'Usuario no encontrado'
                ],
                404
            );
        }

        return response()->json([
            'message' => 'Usuario obtenido correctamente',
            'data' => $user,
        ], 200);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = \App\Models\User::find($id);

        if (!$user) {
            return response()->json(
                [
                    'message' => 'Usuario no encontrado'
                ],
                404
            );
        }

        if ($user->id != $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(
                [
                    'message' => 'No tienes permiso para editar este perfil'
                ],
                403
            );
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $user,
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = \App\Models\User::find($id);

        if (!$user) {
            return response()->json(
                [
                    'message' => 'Usuario no encontrado'
                ],
                404
            );
        }

        if ($user->id != $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(
                [
                    'message' => 'No tienes permiso para eliminar este perfil'
                ],
                403
            );
        }

        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente',
        ], 200);
    }
}
