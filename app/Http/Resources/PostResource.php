<?php

namespace App\Http\Resources;


use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private function storageUrl(string $path): string
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('tigris');
        return $disk->url($path);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'location' => $this->location,
            'country' => $this->country,
            'img' => $this->img ? $this->storageUrl($this->img) : null,
            'description' => $this->description,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'author' => [
                'id' => $this->author->id,
                'firstName' => $this->author->first_name,
                'lastName' => $this->author->last_name,
                'email' => $this->author->email,
                'avatarUrl' => $this->author->avatar ? $this->storageUrl($this->author->avatar) : null,
            ],
            'comments' => $this->comments->map(function (Comment $comment) {
                return [
                    "id"         => $comment->id,
                    "comment"    => $comment->comment,
                    "userId"     => (string) $comment->user_id,
                    "firstName"  => $comment->author->first_name,
                    "lastName"   => $comment->author->last_name,
                    "avatarUrl"  => $comment->author->avatar
                                    ? $this->storageUrl($comment->author->avatar)
                                    : null,
                    "likes"      => $comment->likes->count(),
                    "userLikeId" => $comment->likes->where('user_id', optional(auth('sanctum')->user())->id)->first()?->id ?? null,
                ];
            }),
            'likes' => $this->likes->count(),
            'userLikeId' => $this->likes->where('user_id', optional(auth('sanctum')->user())->id)->first()?->id ?? null,
        ];
    }
}
