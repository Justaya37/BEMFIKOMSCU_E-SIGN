<?php

namespace App\Http\Controllers;

use App\Http\Requests\RejectLetterRequest;
use App\Http\Requests\RevokeLetterRequest;
use App\Http\Requests\SignLetterRequest;
use App\Models\Letter;
use App\Services\AuditLogService;
use App\Services\PdfSigningService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ApprovalController extends Controller
{
    /**
     * Display approval queue for pending letters.
     */
    public function index(Request $request): View
    {
        $query = Letter::where('status', 'pending')
            ->with(['recipients', 'submitter']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('letter_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest()->paginate(10)->withQueryString();
        $pendingCount = Letter::where('status', 'pending')->count();

        return view('approvals.index', compact('letters', 'pendingCount'));
    }

    /**
     * Split-screen review page for a letter.
     */
    public function show(Letter $letter): View
    {
        $letter->load(['recipients', 'submitter', 'approver', 'auditLogs.user']);

        return view('approvals.show', compact('letter'));
    }

    /**
     * Sign and stamp the letter (Action 1).
     */
    public function sign(
        SignLetterRequest $request,
        Letter $letter,
        PdfSigningService $signingService
    ): RedirectResponse {
        if (!$letter->isPending()) {
            return back()->with('error', "Surat ini tidak berstatus 'Menunggu Pengesahan'. Status saat ini: {$letter->status_label}.");
        }

        $user = auth()->user();

        // Verify 6-digit signing PIN
        if (!$user->signing_pin || !Hash::check($request->input('signing_pin'), $user->signing_pin)) {
            return back()->withInput()->with('pin_error', 'PIN Pengesahan 6 digit yang Anda masukkan salah. Pengesahan dibatalkan.');
        }

        // Allow signer to fine-tune placement and target page before finalizing
        if ($request->filled('cap_x') || $request->filled('cap_y') || $request->filled('qr_position') || $request->filled('stamp_page')) {
            $letter->fill([
                'qr_position' => $request->input('qr_position', $letter->qr_position ?: 'bottom_left'),
                'cap_x' => (float) ($request->input('cap_x') ?? $letter->cap_x ?? 130.0),
                'cap_y' => (float) ($request->input('cap_y') ?? $letter->cap_y ?? 200.0),
                'cap_width' => (float) ($request->input('cap_width') ?? $letter->cap_width ?? 38.0),
                'include_signature_img' => $request->has('include_signature_img')
                    ? $request->boolean('include_signature_img')
                    : (bool) $letter->include_signature_img,
                'stamp_page' => $request->filled('stamp_page')
                    ? max(1, (int) $request->input('stamp_page'))
                    : max(1, (int) ($letter->stamp_page ?? 1)),
            ]);
            $letter->save();
        }

        try {
            $signingResult = $signingService->signLetter($letter, $user);

            return redirect()->route('approvals.show', $letter)
                ->with('status', "Surat resmi {$letter->letter_number} BERHASIL DISAHKAN secara digital! Kriptografi X.509, Stempel Cap BEM, QR Code, dan Sidik Jari SHA-256 telah disematkan.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses penandatanganan PDF: ' . $e->getMessage());
        }
    }

    /**
     * Reject the letter (Action 2).
     */
    public function reject(RejectLetterRequest $request, Letter $letter): RedirectResponse
    {
        if (!$letter->isPending()) {
            return back()->with('error', 'Hanya surat dengan status Menunggu yang dapat ditolak.');
        }

        $user = auth()->user();
        $reason = trim($request->input('reason'));

        $letter->update([
            'status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_or_revoke_reason' => $reason,
        ]);

        AuditLogService::log('REJECTED', $letter, $user, [
            'reason' => $reason,
            'rejected_by' => $user->name,
            'jabatan' => $user->jabatan,
        ]);

        return redirect()->route('approvals.show', $letter)
            ->with('status', "Pengesahan surat resmi {$letter->letter_number} telah DITOLAK dengan alasan: {$reason}.");
    }

    /**
     * Revoke endorsement on an already approved letter (Action 3).
     */
    public function revoke(RevokeLetterRequest $request, Letter $letter): RedirectResponse
    {
        if (!$letter->isApproved()) {
            return back()->with('error', 'Hanya surat yang sudah disahkan yang dapat dicabut pengesahannya.');
        }

        $user = auth()->user();

        // Verify PIN
        if (!$user->signing_pin || !Hash::check($request->input('signing_pin'), $user->signing_pin)) {
            return back()->withInput()->with('pin_error', 'PIN Otorisasi 6 digit yang Anda masukkan salah. Pencabutan dibatalkan.');
        }

        $reason = trim($request->input('reason'));

        $letter->update([
            'status' => 'revoked',
            'rejection_or_revoke_reason' => $reason,
        ]);

        AuditLogService::log('REVOKED', $letter, $user, [
            'reason' => $reason,
            'revoked_by' => $user->name,
            'jabatan' => $user->jabatan,
            'previous_hash' => $letter->file_hash,
        ]);

        return redirect()->route('approvals.show', $letter)
            ->with('status', "PENGESAHAN SURAT DICABUT SECARA RESMI. Halaman verifikasi publik dan scan QR Code otomatis menampilkan status surat dicabut.");
    }
}
