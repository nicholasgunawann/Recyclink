<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSellerProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSeller() ?? false;
    }

    public function rules(): array
    {
        return [
            'business_name' => 'required|string|max:150',
            'business_type' => 'nullable|string|max:100',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100', // Changed to nullable since city might not be explicitly inputted in simple form
            'province' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ];
    }
}
