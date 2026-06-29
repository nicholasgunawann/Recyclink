<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBuyerProfileRequest extends FormRequest
{
    // ponytail: buyer role only
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user()?->id,
            'password' => 'nullable|string|min:8',
            'address' => 'required|string',
            'company_name' => 'nullable|string|max:150',
            'industry_type' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
        ];
    }
}
