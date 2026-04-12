<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;

CommentController:
/**
 * @group Comments
 */



class CommentController extends Controller
{

        /**
     * Create a comment
     * 
     * Create a new comment for a specific note. Returns the created comment.
     * 
     */
    public function store(StoreCommentRequest $request, $noteId)
    {

        $comment = Comment::create([
            'note_id' => $noteId,
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Comentario creado correctamente',
            'data' => $comment,
        ], 201);
    }

        /**
     * List all comments for a note
     * 
     * Returns a list of all comments for a specific note.
     * 
     */

    public function index($noteId)
    {
        $comments = Comment::where('note_id', $noteId)->with('user')->get();

        return response()->json([
            'message' => 'Comentarios obtenidos correctamente',
            'data' => $comments,
        ], 200);
    }

        /**
     * Update a comment
     * 
     * Update the content of an existing comment. Returns the updated comment.
     * 
     */

    public function update(UpdateCommentRequest $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(
                [
                    'message' => 'Comentario no encontrado'
                ],
                404
            );
        }

        if ($comment->user_id != $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(
                [
                    'message' => 'No tienes permiso para editar este comentario'
                ],
                403
            );
        }

        $comment->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'message' => 'Comentario actualizado correctamente',
            'data' => $comment,
        ], 200);
    }

        /**
     * Delete a comment
     * 
     * Delete an existing comment. Returns a success message.
     * 
     */

    public function destroy(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(
                [
                    'message' => 'Comentario no encontrado'
                ],
                404
            );
        }

        if ($comment->user_id != $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(
                [
                    'message' => 'No tienes permiso para editar este comentario'
                ],
                403
            );
        }

        $comment->delete();

        return response()->json([
            'message' => 'Comentario eliminado correctamente',
        ], 200);
    }
}
