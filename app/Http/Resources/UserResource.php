<?php

namespace App\Http\Resources;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostResource;

class UserResource extends JsonResource
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
            "id" => $this->id,
            "firstName" => $this->first_name,
            "lastName" => $this->last_name,
            "email" => $this->email,
            "avatarUrl" => $this->avatar ? $this->storageUrl($this->avatar) : null,
            "createdAt" => $this->created_at,
            "updatedAt" => $this->updated_at,
            "posts" => PostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
