<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use Illuminate\Http\Request;
use App\Models\Note;

NoteController:
/**
 * @group Notes
 */


class Notecontroller extends Controller
{
    /**
     * Create a note
     * 
     * Create a new note with the provided information. Returns the created note.
     * 
     */
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

    /**
     * List all notes
     * 
     * Return all notes from the authenticated user.
     */
    public function index(Request $request)
    {
        $query = Note::where('user_id', $request->user()->id);

        $status = $request->query('status');

        if ($status === 'completed') {
            $query->where('is_completed', true);
        } elseif ($status === 'active') {
            $query->where('is_completed', false);
        }

        $notes = $query->get();

        return response()->json([
            'message' => 'Notas obtenidas correctamente',
            'data' => $notes,
        ], 200);
    }

    /**
     * Show a note
     * 
     * Return the details of a specific note.
     * 
     */
    public function show(Request $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json(
                [
                    'message' => 'Nota no encontrada'
                ],
                404
            );
        }

        return response()->json([
            'message' => 'Nota obtenida correctamente',
            'data' => $note,
        ], 200);
    }

    /**
     * Update a note
     * 
     * Update the title, description, category, or event date of an existing note. Returns the updated note.
     */
    public function update(UpdateNoteRequest $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json(
                [
                    'message' => 'Nota no encontrada'
                ],
                404
            );
        }

        if ($note->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(
                [
                    'message' => 'No autorizado'
                ],
                403
            );
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

    /**
     * Delete a note
     * 
     * Delete an existing note. Returns a success message.
     * 
     */
    public function destroy(Request $request, $id)
    {
        $note = Note::find($id);


        if (!$note) {
            return response()->json(
                [
                    'message' => 'Nota no encontrada'
                ],
                404
            );
        }

        if ($note->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(
                [
                    'message' => 'No autorizado'
                ],
                403
            );
        }

        $note->delete();

        return response()->json([
            'message' => 'Nota eliminada correctamente',
        ], 200);
    }


    /**
     * Complete a note
     * 
     * Mark a note as completed. Returns the updated note.
     * 
     */
    public function complete(Request $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json(
                [
                    'message' => 'Nota no encontrada'
                ],
                404
            );
        }

        if ($note->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(
                [
                    'message' => 'No autorizado'
                ],
                403
            );
        }

        $note->update(['is_completed' => true]);

        return response()->json([
            'message' => 'Nota marcada como completada',
            'data' => $note,
        ], 200);
    }

        /**
     * Reopen a note
     * 
     * Reopen a completed note. Returns the updated note.
     * 
     */
    public function reopen(Request $request, $id)
    {
        $note = Note::find($id);

        if (!$note) {
            return response()->json(
                [
                    'message' => 'Nota no encontrada'
                ],
                404
            );
        }
        if ($note->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(
                [
                    'message' => 'No autorizado'
                ],
                403
            );
        }

        $note->update(['is_completed' => false]);

        return response()->json([
            'message' => 'Nota reabierta correctamente',
            'data' => $note,
        ], 200);
    }
}
