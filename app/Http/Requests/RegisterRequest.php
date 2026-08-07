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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            // Buyer specific fields
            'company_name' => 'required_if:role,buyer|nullable|string|max:255',
            'industry_type' => 'required_if:role,buyer|nullable|string|max:100',
            // Seller specific fields
            'business_name' => 'required_if:role,seller|nullable|string|max:255',
            'business_type' => 'required_if:role,seller|nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap PIC wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'phone_number.required' => 'No. WhatsApp wajib diisi.',
            'role.required' => 'Silakan pilih peran akun (Pembeli atau Penjual).',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'city.required' => 'Kota/Kabupaten wajib diisi.',
            'province.required' => 'Provinsi wajib diisi.',
            'postal_code.required' => 'Kode pos wajib diisi.',
            'company_name.required_if' => 'Nama perusahaan wajib diisi untuk akun pembeli.',
            'industry_type.required_if' => 'Jenis industri wajib diisi untuk akun pembeli.',
            'business_name.required_if' => 'Nama usaha/pengepul wajib diisi untuk akun penjual.',
            'business_type.required_if' => 'Tipe usaha wajib diisi untuk akun penjual.',
        ];
    }
}
