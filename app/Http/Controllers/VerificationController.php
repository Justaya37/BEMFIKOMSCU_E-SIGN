<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VerificationController extends Controller
{
    /**
     * Display letter verification page (accessed via QR code scan or token).
     */
    public function show(Request $request, string $token): View
    {
        $letter = Letter::where('verification_token', $token)
            ->with(['recipients', 'submitter', 'approver'])
            ->firstOrFail();

        // Audit log for QR code / link verification scan
        AuditLogService::log('VERIFIED_QR', $letter, null, [
            'source' => $request->has('qr') ? 'qr_scanner' : 'direct_link',
            'letter_number' => $letter->letter_number,
            'status' => $letter->status,
        ], $request);

        return view('verify', compact('letter'));
    }

    /**
     * Download the official signed PDF (public access for verified letters).
     */
    public function download(string $token): BinaryFileResponse
    {
        $letter = Letter::where('verification_token', $token)->firstOrFail();

        if (!$letter->isApproved() || empty($letter->signed_file_path)) {
            abort(403, 'Dokumen ini belum berstatus sah atau tidak memiliki file bertanda tangan.');
        }

        $fullPath = storage_path('app/private/' . ltrim($letter->signed_file_path, '/\\'));
        if (!File::exists($fullPath)) {
            abort(404, 'File PDF resmi tidak ditemukan di penyimpanan server.');
        }

        AuditLogService::log('DOWNLOADED', $letter, auth()->user(), [
            'via' => 'public_verify',
            'file' => $letter->signed_file_path,
        ]);

        $safeNumber = preg_replace('/[^A-Za-z0-9\-]/', '_', $letter->letter_number);
        $downloadName = "BEM_FIKOM_{$safeNumber}_SAH.pdf";

        return response()->download($fullPath, $downloadName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Stream the signed PDF inline for iframe preview.
     */
    public function preview(string $token): BinaryFileResponse
    {
        $letter = Letter::where('verification_token', $token)->firstOrFail();

        if (!$letter->isApproved() || empty($letter->signed_file_path)) {
            abort(403, 'Dokumen belum berstatus sah.');
        }

        $fullPath = storage_path('app/private/' . ltrim($letter->signed_file_path, '/\\'));
        if (!File::exists($fullPath)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview.pdf"',
        ]);
    }
}
