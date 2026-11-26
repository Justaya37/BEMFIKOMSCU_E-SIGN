<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public homepage with verification tools.
     */
    public function index(Request $request): View
    {
        return view('home');
    }

    /**
     * Quick search by letter number or verification token.
     */
    public function checkQuery(Request $request): RedirectResponse
    {
        $request->validate([
            'letter_query' => ['required', 'string', 'max:100'],
        ], [
            'letter_query.required' => 'Nomor surat atau token verifikasi wajib diisi.',
        ]);

        $query = trim($request->input('letter_query'));

        $letter = Letter::where('verification_token', $query)
            ->orWhere('letter_number', $query)
            ->first();

        if ($letter) {
            return redirect()->route('verify.show', ['token' => $letter->verification_token]);
        }

        $redirectRoute = auth()->check() ? 'dashboard' : 'verify.index';

        return redirect()->route($redirectRoute, ['tab' => 'query'])
            ->withInput()
            ->with('query_error', "Surat dengan nomor/kode '{$query}' tidak ditemukan di sistem BEM FIKOM.");
    }

    /**
     * Check PDF file integrity (Anti-Edit SHA-256 match).
     */
    public function checkIntegrity(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'pdf_file.required' => 'Silakan pilih file PDF yang ingin diperiksa integritasnya.',
            'pdf_file.mimes' => 'Format file harus berupa dokumen PDF (.pdf).',
            'pdf_file.max' => 'Ukuran file PDF maksimal 10MB.',
        ]);

        $file = $request->file('pdf_file');
        $hash = hash_file('sha256', $file->getRealPath());

        $letter = Letter::where('file_hash', $hash)
            ->with(['recipients', 'submitter', 'approver'])
            ->first();

        if ($letter) {
            AuditLogService::log('VERIFIED_HASH_MATCH', $letter, null, [
                'computed_hash' => $hash,
                'matched_letter_number' => $letter->letter_number,
                'status' => $letter->status,
            ], $request);

            if ($request->wantsJson()) {
                return response()->json([
                    'match' => true,
                    'status_title' => 'DOKUMEN ASLI & TERVERIFIKASI (100% UTUH)',
                    'message' => 'Sidik jari SHA-256 file PDF ini cocok sempurna dengan arsip digital BEM FIKOM. Dokumen tidak mengalami modifikasi, crop, atau pengubahan teks.',
                    'hash' => $hash,
                    'letter' => [
                        'letter_number' => $letter->letter_number,
                        'title' => $letter->title,
                        'category_label' => $letter->category_label,
                        'status' => $letter->status,
                        'status_label' => $letter->status_label,
                        'letter_date' => $letter->letter_date->translatedFormat('d F Y'),
                        'event_name' => $letter->event_name,
                        'approved_by' => $letter->approver?->name,
                        'approved_by_jabatan' => $letter->approver?->jabatan,
                        'approved_at' => $letter->approved_at?->format('d/m/Y H:i') . ' WIB',
                        'verification_token' => $letter->verification_token,
                        'verify_url' => route('verify.show', ['token' => $letter->verification_token]),
                    ],
                    'recipients' => $letter->recipients->map(fn ($r) => [
                        'student_name' => $r->student_name,
                        'nim' => $r->nim,
                        'notes' => $r->notes,
                    ]),
                ]);
            }

            return redirect()->route('verify.show', ['token' => $letter->verification_token])
                ->with('integrity_success', "Uji integritas berhasil! File PDF yang Anda unggah 100% identik dengan surat resmi BEM FIKOM (Hash: {$hash}).");
        }

        // Mismatch
        AuditLogService::log('VERIFIED_HASH_MISMATCH', null, null, [
            'computed_hash' => $hash,
            'original_filename' => $file->getClientOriginalName(),
        ], $request);

        if ($request->wantsJson()) {
            return response()->json([
                'match' => false,
                'status_title' => 'DOKUMEN TIDAK TERDAFTAR ATAU TELAH DIMODIFIKASI',
                'message' => 'Sidik jari kriptografi (Hash SHA-256) file ini TIDAK DITEMUKAN di database resmi BEM FIKOM. Kemungkinan file pernah diedit, teks diubah, di-convert ulang, atau bukan surat resmi yang diterbitkan sistem.',
                'hash' => $hash,
            ], 422);
        }

        return redirect()->route('home', ['tab' => 'integrity'])
            ->with('integrity_error', "PERINGATAN: File PDF tidak cocok dengan arsip resmi manapun (Hash: {$hash}). File mungkin telah dimodifikasi atau tidak sah.");
    }
}
