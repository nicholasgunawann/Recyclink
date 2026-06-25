<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEducationContentRequest extends FormRequest
{
    // ponytail: admin role only
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:150',
            'content' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'content_type' => 'required|string|in:article,video,guide',
            'status' => 'nullable|string|in:draft,published,archived',
        ];
    }
}
