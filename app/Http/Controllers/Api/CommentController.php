<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Http\Requests\StoreCommentRequest;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, $noteId){

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

    public function index($noteId){
        $comments = Comment::where('note_id', $noteId)->with('user')->get();

        return response()->json([
            'message' => 'Comentarios obtenidos correctamente',
            'data' => $comments,
        ], 200);
    }
}
