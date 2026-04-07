<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, $noteId){

        $comment = Comment::create([
            'note_id' => $noteId,
            'user_id' => $request->user()->id,
            'content' => $request->content,
        ]);

        response()->json([
            'message' => 'Comentario creado correctamente',
            'data' => $comment,
        ], 201);
    }
}
