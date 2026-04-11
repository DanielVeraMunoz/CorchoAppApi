<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ThankController extends Controller
{
    public function store(Request $request, $recipientId){
         
    $request->validate([
            'note_id' => 'required|exists:notes,id',
            'message' => 'nullable|string|max:255',
        ]);

        $note = \App\Models\Note::find($request->input('note_id'));

        if(!$note){
            return response()->json([
                'message' => 'Nota no encontrada',
            ], 404);
        }
        
    
        if($note->user_id !== $request->user()->id){
            return response()->json(403);
        }

        $thank = \App\Models\Thank::create([
            'note_id' => $request->input('note_id'),
            'recipient_id' => $recipientId,
            'giver_id' => $request->user()->id,
            'message' => $request->input('message'),
        ]);

        return response()->json([
            'message' => 'Gracias por tu aporte!',
            'data' => $thank,
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
