<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMessageRequest extends FormRequest
{
    // ponytail: buyer or seller role check
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isBuyer() || $user->isSeller());
    }

    public function rules(): array
    {
        return [
            'message_text' => 'required_without:image|nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'message_type' => 'nullable|string|in:text,image,offer',
        ];
    }
}
