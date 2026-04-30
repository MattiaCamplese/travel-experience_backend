<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {
        $data = $request->validated();

        $comment = Comment::create($data);

        return response()->json([
            'succes' => true,
            'message' => 'Commento Creato con Successo',
            'data' => $comment
        ]);
    }

     public function destroy(Comment $comment)
    {
        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commento Eliminato Correttamente.',
            'data' => []
        ]);
    }
}
