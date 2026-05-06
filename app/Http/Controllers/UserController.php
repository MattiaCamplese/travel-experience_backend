<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create($data);

        return response()->json([
            'succes' => true,
            'message' =>'Utente Creato con Successo',
            'data'=>new UserResource ($user->fresh())
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['posts.author', 'posts.comments.author', 'posts.comments.likes', 'posts.likes']);
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'firstName' => ['sometimes', 'string', 'max:255'],
            'lastName'  => ['sometimes', 'string', 'max:255'],
            'password'  => ['sometimes', 'confirmed', 'min:8'],
        ]);

        if (isset($data['firstName'])) $user->first_name = $data['firstName'];
        if (isset($data['lastName']))  $user->last_name  = $data['lastName'];
        if (isset($data['password']))  $user->password   = $data['password'];

        $user->save();

        return new UserResource($user);
    }

    public function updateAvatar(Request $request, User $user)
    {
        $request->validate(['avatar' => 'required|image|max:2048']);

        if ($user->avatar) {
            Storage::disk('tigris')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'tigris');
        $user->update(['avatar' => $path]);

        $avatarUrl = 'https://' . env('AWS_BUCKET') . '.fly.storage.tigris.dev/' . $path;

        return response()->json([
            'success' => true,
            'data' => ['avatarUrl' => $avatarUrl],
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Utente Eliminato Correttamente.',
            'data' => []
        ]);
    }
}
