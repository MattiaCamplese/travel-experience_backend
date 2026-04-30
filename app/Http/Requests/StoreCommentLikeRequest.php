<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentLikeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'    => ['required', 'exists:App\Models\User,id'],
            'comment_id' => ['required', 'exists:App\Models\Comment,id'],
        ];
    }
}
