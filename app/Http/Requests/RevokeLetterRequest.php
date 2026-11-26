<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RevokeLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canSign();
    }

    public function rules(): array
    {
        return [
            'signing_pin' => ['required', 'digits:6'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'signing_pin.required' => 'PIN Pengesahan 6 digit wajib dimasukkan untuk verifikasi otoritas.',
            'signing_pin.digits' => 'PIN Pengesahan harus 6 digit angka.',
            'reason.required' => 'Alasan pencabutan pengesahan surat wajib dijelaskan secara formal.',
            'reason.min' => 'Alasan pencabutan minimal 5 karakter.',
        ];
    }
}
