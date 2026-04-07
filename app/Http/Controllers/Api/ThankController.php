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
}
