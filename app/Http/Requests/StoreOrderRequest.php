<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    // ponytail: buyer role only
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() ?? false;
    }

    public function rules(): array
    {
        return [
            'quantity' => 'required|numeric|min:0.01',
            'pickup_method' => 'nullable|string|in:self_pickup,delivery',
            'pickup_date' => 'nullable|date|after_or_equal:today',
            'pickup_time' => 'nullable|date_format:H:i',
            'pickup_address' => 'nullable|string',
            'buyer_note' => 'nullable|string|max:1000',
        ];
    }
}
