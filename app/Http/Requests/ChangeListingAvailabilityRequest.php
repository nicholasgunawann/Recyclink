<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeListingAvailabilityRequest extends FormRequest
{
    // ponytail: seller role only
    public function authorize(): bool
    {
        return $this->user()?->isSeller() ?? false;
    }

    public function rules(): array
    {
        return [
            'availability_status' => 'required|string|in:available,sold_out,inactive',
        ];
    }
}
