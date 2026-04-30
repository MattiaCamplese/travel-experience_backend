<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentLikeRequest;
use App\Models\CommentLike;

class CommentLikeController extends Controller
{
    public function store(StoreCommentLikeRequest $request)
    {
        $data = $request->validated();

        $like = CommentLike::firstOrCreate($data);

        return response()->json([
            'success' => true,
            'message' => 'Like al commento creato con successo',
            'data'    => $like,
        ]);
    }

    public function destroy(CommentLike $commentLike)
    {
        $commentLike->delete();

        return response()->json([
            'success' => true,
            'message' => 'Like al commento eliminato correttamente.',
            'data'    => [],
        ]);
    }
}
