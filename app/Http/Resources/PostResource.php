<?php

namespace App\Http\Resources;


use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'location' => $this->location,
            'country' => $this->country,
            'img' => $this->img ? Storage::disk('tigris')->url($this->img) : null,
            'description' => $this->description,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'author' => [
                'id' => $this->author->id,
                'firstName' => $this->author->first_name,
                'lastName' => $this->author->last_name,
                'email' => $this->author->email,
                'avatarUrl' => $this->author->avatar ? Storage::disk('tigris')->url($this->author->avatar) : null,
            ],
            'comments' => $this->comments->map(function (Comment $comment) {
                return [
                    "id"         => $comment->id,
                    "comment"    => $comment->comment,
                    "firstName"  => $comment->author->first_name,
                    "lastName"   => $comment->author->last_name,
                    "likes"      => $comment->likes->count(),
                    "userLikeId" => $comment->likes->where('user_id', optional(auth('sanctum')->user())->id)->first()?->id ?? null,
                ];
            }),
            'likes' => $this->likes->count(),
            'userLikeId' => $this->likes->where('user_id', optional(auth('sanctum')->user())->id)->first()?->id ?? null,
        ];
    }
}
