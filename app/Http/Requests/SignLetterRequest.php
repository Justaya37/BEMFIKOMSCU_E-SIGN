<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canSign();
    }

    public function rules(): array
    {
        return [
            'signing_pin' => ['required', 'digits:6'],
            'qr_position' => ['nullable', 'in:bottom_left,bottom_right,bottom_center'],
            'cap_x' => ['nullable', 'numeric', 'min:0', 'max:250'],
            'cap_y' => ['nullable', 'numeric', 'min:0', 'max:350'],
            'cap_width' => ['nullable', 'numeric', 'min:15', 'max:120'],
            'include_signature_img' => ['nullable'],
            'stamp_page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'signing_pin.required' => 'PIN Pengesahan 6 digit wajib dimasukkan.',
            'signing_pin.digits' => 'PIN Pengesahan harus terdiri dari tepat 6 digit angka.',
        ];
    }
}
