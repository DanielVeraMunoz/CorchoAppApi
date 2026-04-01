<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;

class Notecontroller extends Controller
{
    public function store(Request $request){

        $note = Note::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'event_date' => $request->event_date,
            'is_completed' => false,
        ]);

        return response()->json([
            'message' => 'Nota creada correctamente',
            'data' => $note,
        ], 201);


    }
}
