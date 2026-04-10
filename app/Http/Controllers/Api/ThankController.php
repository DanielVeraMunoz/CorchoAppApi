<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThankController extends Controller
{
    public function store(Request $request, $noteId){
        
        $thanks = \App\Models\Thank::create([
            'note_id' => $noteId,
            'giver_id' => $request->user()->id,
            'recipient_id' => \App\Models\Note::find($noteId)->user_id,
        ]);

        return response()->json([
            'message' => 'Gracias enviadas correctamente',
            'data' => $thanks,
        ], 201);
    }

    public function index(Request $request, $noteId){
        $thanks = \App\Models\Thank::where('note_id', $noteId)->with('giver')->get();

        return response()->json([
            'message' => 'Gracias obtenidas correctamente',
            'data' => $thanks,
        ], 200);
    }


    public function destroy(Request $request, $id){
        $thank = \App\Models\Thank::find($id);

        if(!$thank){
            return response()->json([
                'message' => 'Gracias no encontrada',
            ], 404);
        }

        if($thank->giver_id !== $request->user()->id && $request->user()->role !== 'admin'){
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta gracias',
            ], 403);
        }

        $thank->delete();

        return response()->json([
            'message' => 'Agradecimiento eliminado correctamente',
        ], 204);
    }
}
