<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLikeRequest;
use App\Models\Like;


class LikeController extends Controller
{
    public function store(StoreLikeRequest $request)
    {
        $data = $request->validated();

        $like = Like::firstOrCreate($data);

        return response()->json([
            'succes' => true,
            'message' => 'Like Creato con Successo',
            'data' => $like
        ]);
    }

     public function destroy(Like $like)
    {
        $like->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commento Eliminato Correttamente.',
            'data' => []
        ]);
    }
}
