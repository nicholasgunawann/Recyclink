<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWasteListingRequest extends FormRequest
{
    // ponytail: seller role only
    public function authorize(): bool
    {
        return $this->user()?->isSeller() ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:waste_categories,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit' => 'required|string|in:kg,liter,pcs,karung',
            'price_per_unit' => 'required|numeric|min:0',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
