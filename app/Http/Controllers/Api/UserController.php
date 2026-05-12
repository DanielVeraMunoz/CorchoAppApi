<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

UserController:
/**
 * @group Users
 */


class UserController extends Controller
{
    /**
     * List all users
     * 
     * Return a list of all users in the community.
     */
    public function index(Request $request)
    {
        $users = \App\Models\User::where('community_id', $request->user()->community_id)->get();    

        return response()->json([
            'message' => 'Usuarios obtenidos correctamente',
            'data' => $users,
        ], 200);
    }

    /**
     * Show a user
     * 
     * Return the details of a specific user.
     * 
     */
    public function show(Request $request, $id)
    {
        $user = \App\Models\User::where('id', $id)->where('community_id', $request->user()->community_id)->first();

        if (!$user) {
            return response()->json(
                [
                    'message' => 'Usuario no encontrado'
                ],
                404
            );
        }

        $user->load(['community']);

        return response()->json([
            'message' => 'Usuario obtenido correctamente',
            'data' => $user,
        ], 200);
    }

    /**
     * Update a user
     * 
     * Update the information of an existing user. Returns the updated user.
     * 
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = \App\Models\User::where('id', $id)->where('community_id', $request->user()->community_id)->first();

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

        $data = $request->only(['name', 'email', 'floor', 'door']);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        $user->load(['community']);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $user,
        ], 200);
    }


    /**
     * Delete a user
     * 
     * Delete an existing user. Returns a success message.
     * 
     */
    public function destroy(Request $request, $id)
    {
        $user = \App\Models\User::where('id', $id)->where('community_id', $request->user()->community_id)->first();

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
