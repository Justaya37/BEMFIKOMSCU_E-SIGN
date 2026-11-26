<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLetterRequest;
use App\Models\Letter;
use App\Models\LetterRecipient;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LetterController extends Controller
{
    /**
     * Show form for submitting a new letter for approval.
     */
    public function create(): View
    {
        return view('letters.create');
    }

    /**
     * Store new draft letter and recipient list.
     */
    public function store(StoreLetterRequest $request): RedirectResponse
    {
        $draftsDir = storage_path('app/private/drafts');
        File::ensureDirectoryExists($draftsDir);

        $uploadedFile = $request->file('draft_pdf');
        $fileName = 'draft_' . time() . '_' . Str::random(10) . '.pdf';
        $uploadedFile->move($draftsDir, $fileName);

        $token = (string) Str::uuid();

        $letter = Letter::create([
            'verification_token' => $token,
            'letter_number' => trim($request->input('letter_number')),
            'title' => trim($request->input('title')),
            'category' => $request->input('category'),
            'letter_date' => $request->input('letter_date'),
            'event_name' => trim($request->input('event_name')),
            'draft_file_path' => 'drafts/' . $fileName,
            'status' => 'pending',
            'submitted_by' => auth()->id(),
            'stamp_page' => max(1, (int) ($request->input('stamp_page', 1) ?: 1)),
            'stamp_position' => $request->input('stamp_position', 'bottom_right'),
            'qr_position' => $request->input('qr_position', 'bottom_left'),
            'cap_x' => (float) ($request->input('cap_x') ?? 130.0),
            'cap_y' => (float) ($request->input('cap_y') ?? 200.0),
            'cap_width' => (float) ($request->input('cap_width') ?? 38.0),
            'include_signature_img' => $request->boolean('include_signature_img', false),
        ]);

        // Save recipient list
        $recipientsData = $request->input('recipients', []);
        if (is_array($recipientsData)) {
            foreach ($recipientsData as $row) {
                if (!empty($row['student_name']) && !empty($row['nim'])) {
                    LetterRecipient::create([
                        'letter_id' => $letter->id,
                        'student_name' => trim($row['student_name']),
                        'nim' => trim($row['nim']),
                        'notes' => !empty($row['notes']) ? trim($row['notes']) : null,
                    ]);
                }
            }
        }

        // Log audit
        AuditLogService::log('SUBMITTED', $letter, auth()->user(), [
            'letter_number' => $letter->letter_number,
            'recipients_count' => count($recipientsData),
            'draft_file' => $letter->draft_file_path,
        ]);

        return redirect()->route('letters.show', $letter)
            ->with('status', "Surat nomor {$letter->letter_number} berhasil diajukan dan masuk ke antrean pengesahan Ketua/Wakil.");
    }

    /**
     * Show letter details, timeline, and embedded preview.
     */
    public function show(Letter $letter): View
    {
        $letter->load(['recipients', 'submitter', 'approver', 'auditLogs.user']);

        return view('letters.show', compact('letter'));
    }

    /**
     * Download signed PDF (or draft PDF if pending/rejected).
     */
    public function download(Letter $letter): BinaryFileResponse
    {
        $filePath = null;
        $downloadName = null;

        if ($letter->isApproved() && $letter->signed_file_path) {
            $filePath = storage_path('app/private/' . ltrim($letter->signed_file_path, '/\\'));
            $safeNum = preg_replace('/[^A-Za-z0-9\-]/', '_', $letter->letter_number);
            $downloadName = "BEM_FIKOM_{$safeNum}_SAH.pdf";
        } else {
            // Only authenticated users can download draft
            if (!auth()->check()) {
                abort(403);
            }
            $filePath = storage_path('app/private/' . ltrim($letter->draft_file_path, '/\\'));
            $safeNum = preg_replace('/[^A-Za-z0-9\-]/', '_', $letter->letter_number);
            $downloadName = "DRAF_BEM_FIKOM_{$safeNum}.pdf";
        }

        if (!$filePath || !File::exists($filePath)) {
            abort(404, 'File dokumen tidak ditemukan di server.');
        }

        AuditLogService::log('DOWNLOADED', $letter, auth()->user(), [
            'type' => $letter->isApproved() ? 'signed' : 'draft',
            'file' => $filePath,
        ]);

        return response()->download($filePath, $downloadName, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Stream PDF for iframe embedded viewer.
     */
    public function streamFile(Letter $letter, string $type): BinaryFileResponse
    {
        $relativePath = ($type === 'signed' && $letter->signed_file_path)
            ? $letter->signed_file_path
            : $letter->draft_file_path;

        $fullPath = storage_path('app/private/' . ltrim($relativePath, '/\\'));

        if (!File::exists($fullPath)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($fullPath) . '"',
            'Cache-Control' => 'no-cache, private',
        ]);
    }
}
