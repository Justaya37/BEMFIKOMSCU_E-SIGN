<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLetterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'letter_number' => ['required', 'string', 'max:100', 'unique:letters,letter_number'],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:surat_izin,surat_tugas,surat_undangan,surat_keterangan,lainnya'],
            'letter_date' => ['required', 'date'],
            'event_name' => ['required', 'string', 'max:255'],
            'stamp_page' => ['required', 'integer', 'min:1'],
            'stamp_position' => ['nullable', 'in:bottom_right,bottom_left,bottom_center'],
            'qr_position' => ['nullable', 'in:bottom_left,bottom_right,bottom_center'],
            'cap_x' => ['nullable', 'numeric', 'min:0', 'max:250'],
            'cap_y' => ['nullable', 'numeric', 'min:0', 'max:350'],
            'cap_width' => ['nullable', 'numeric', 'min:15', 'max:120'],
            'include_signature_img' => ['nullable'],
            'draft_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'recipients' => ['nullable', 'array'],
            'recipients.*.student_name' => ['required', 'string', 'max:255'],
            'recipients.*.nim' => ['required', 'string', 'max:50'],
            'recipients.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'letter_number.required' => 'Nomor surat resmi wajib diisi.',
            'letter_number.unique' => 'Nomor surat ini sudah terdaftar sebelumnya.',
            'title.required' => 'Perihal / Judul surat wajib diisi.',
            'category.required' => 'Kategori surat wajib dipilih.',
            'letter_date.required' => 'Tanggal surat resmi wajib diisi.',
            'event_name.required' => 'Nama kegiatan / dasar surat wajib diisi.',
            'stamp_page.required' => 'Halaman target penempelan cap dan QR code wajib ditentukan.',
            'stamp_page.min' => 'Halaman target penempelan minimal halaman 1.',
            'draft_pdf.required' => 'File draf PDF wajib diunggah.',
            'draft_pdf.mimes' => 'Format file wajib berupa dokumen PDF (.pdf).',
            'draft_pdf.max' => 'Ukuran file draf PDF maksimal 10 Megabytes (10MB).',
            'recipients.*.student_name.required' => 'Nama mahasiswa pada daftar penerima wajib diisi.',
            'recipients.*.nim.required' => 'NIM mahasiswa pada daftar penerima wajib diisi.',
        ];
    }
}
