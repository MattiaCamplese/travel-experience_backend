<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostResource::collection(Post::with(['author', 'comments.author', 'comments.likes', 'likes'])->latest()->paginate(9));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $data = $request->validated();

        $imgPath = null;
        try {
            if ($request->hasFile('img')) {
                $imgPath = $request->file('img')->store('posts', 'tigris');
            }

            $data = $request->validated();
            $data['img'] = $imgPath;

            $post = Post::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Post Created Successfully.',
                'post' => new PostResource($post->fresh())
            ]);
        } catch (\Throwable) {
            if ($imgPath) {
                Storage::disk('tigris')->delete($imgPath);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $data = $request->validated();

        $oldImagePath = $post->img;
        $newImagePath = $oldImagePath;

        try {
            $data = $request->validated();

            if ($request->hasFile('img')) {
                $newImagePath = $request->file('img')->store('posts', 'tigris');
            }

            if ($newImagePath) {
                $data['img'] = $newImagePath;
            }

            $post->update($data);

            if ($request->hasFile('img') && $oldImagePath && $newImagePath !== $oldImagePath) {
                Storage::disk('tigris')->delete($oldImagePath);
            }

            return new PostResource($post);

        } catch (\Throwable $th) {
            if ($request->hasFile('img') && $newImagePath && $newImagePath !== $oldImagePath) {
                Storage::disk('tigris')->delete($newImagePath);
            }
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $imagePath = $post->img;

        $post->delete();

        if ($imagePath) {
            Storage::disk('tigris')->delete($imagePath);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully.',
            'data' => []
        ]);
    }
}
