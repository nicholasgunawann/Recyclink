<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    // ponytail: buyer role only
    public function authorize(): bool
    {
        return $this->user()?->isBuyer() ?? false;
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|string|max:50',
            'payment_gateway' => 'nullable|string|max:50',
            'payment_reference' => 'nullable|string|max:100',
        ];
    }
}
