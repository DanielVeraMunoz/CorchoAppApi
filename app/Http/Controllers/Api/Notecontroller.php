<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use Illuminate\Http\Request;
use App\Models\Note;

class Notecontroller extends Controller
{
    public function store(StoreNoteRequest $request)
    {

        $note = Note::create([
            'user_id' => $request->user()->id,
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


    public function index(Request $request)
    {
        $notes = Note::where('user_id', $request->user()->id)->get();

        return response()->json([
            'message' => 'Notas obtenidas correctamente',
            'data' => $notes,
        ], 200);
    }

    public function show(Request $request, $id){
        $note = Note::find($id);

        if (!$note){
            return response()->json([
                'message' => 'Nota no encontrada'],
                404);
        }

        return response()->json([
            'message' => 'Nota obtenida correctamente',
            'data' => $note,
        ], 200);

    }

    public function update(UpdateNoteRequest $request, $id){
        $note = Note::find($id);

        if (!$note){
            return response()->json([
                'message' => 'Nota no encontrada'],
                404);
        }

        if ($note->user_id !== $request->user()->id){
            return response()->json([
                'message' => 'No autorizado'],
                403);
        }

        $note->update([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
        ]);

        return response()->json([
            'message' => 'Nota actualizada correctamente',
            'data' => $note,
        ], 200);
    }   

}