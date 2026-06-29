<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    // ponytail: open for guest users
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|in:buyer,seller',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            // Buyer specific fields
            'company_name' => 'required_if:role,buyer|nullable|string|max:255',
            'industry_type' => 'required_if:role,buyer|nullable|string|max:100',
            // Seller specific fields
            'business_name' => 'required_if:role,seller|nullable|string|max:255',
            'business_type' => 'required_if:role,seller|nullable|string|max:100',
        ];
    }
}
