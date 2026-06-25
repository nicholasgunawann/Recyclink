<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreComplaintRequest extends FormRequest
{
    // ponytail: buyer or seller participant role check
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isBuyer() || $user->isSeller());
    }

    public function rules(): array
    {
        return [
            'complaint_type' => 'required|string|max:100',
            'description' => 'required|string|min:10',
            'evidence' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
