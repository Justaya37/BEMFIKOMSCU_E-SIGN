<?php

namespace App\Services;

use App\Models\Letter;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\Tcpdf\Fpdi;

class PdfSigningService
{
    /**
     * Sign and stamp a draft letter PDF.
     *
     * @throws Exception
     */
    public function signLetter(Letter $letter, User $signer): array
    {
        // 1. Resolve source draft PDF path
        $draftFullPath = storage_path('app/private/' . ltrim($letter->draft_file_path, '/\\'));
        if (!File::exists($draftFullPath)) {
            throw new Exception("File draf PDF tidak ditemukan di storage privat: {$letter->draft_file_path}");
        }

        // 2. Ensure assets and certificates exist
        $this->ensureCertificatesExist();
        $this->ensureAssetsExist($signer);

        $certPath = storage_path('app/private/certs/bem_fikom.crt');
        $keyPath = storage_path('app/private/certs/bem_fikom.key');
        $capPath = storage_path('app/private/assets/cap_bem.png');

        $sigPath = $signer->signature_image_path
            ? storage_path('app/private/' . ltrim($signer->signature_image_path, '/\\'))
            : storage_path('app/private/signatures/ttd_' . ($signer->isKetua() ? 'ketua' : 'wakil') . '.png');

        if (!File::exists($sigPath)) {
            $sigPath = storage_path('app/private/signatures/ttd_ketua.png');
        }

        // 3. Prepare destination signed file
        $signedDir = storage_path('app/private/signed');
        File::ensureDirectoryExists($signedDir);
        $signedFileName = $letter->verification_token . '.pdf';
        $signedFullPath = $signedDir . DIRECTORY_SEPARATOR . $signedFileName;

        // 4. Initialize FPDI with TCPDF engine
        $pdf = new Fpdi('P', 'mm', 'A4', true, 'UTF-8', false);

        // Metadata
        $pdf->SetCreator('BEM FIKOM E-Sign Engine');
        $pdf->SetAuthor('BEM Fakultas Ilmu Komputer');
        $pdf->SetTitle('Surat Resmi Disahkan - ' . $letter->letter_number);
        $pdf->SetSubject($letter->title);
        $pdf->SetKeywords('E-Sign, BEM FIKOM, X.509, SHA-256, Validated');

        // Turn off default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(false);

        // 5. Embed Cryptographic Digital Signature (X.509 / PKCS#7)
        if (File::exists($certPath) && File::exists($keyPath)) {
            $certUri = 'file://' . str_replace('\\', '/', realpath($certPath));
            $keyUri = 'file://' . str_replace('\\', '/', realpath($keyPath));

            $sigInfo = [
                'Name' => $signer->name,
                'Location' => 'BEM Fakultas Ilmu Komputer',
                'Reason' => 'Pengesahan Surat Resmi: ' . $letter->letter_number,
                'ContactInfo' => 'esign@bemfikom.ac.id',
            ];

            // Mode 2 = self-signed / custom cert with approval
            $pdf->setSignature($certUri, $keyUri, '', '', 2, $sigInfo);
        }

        // 6. Import pages from source draft PDF
        $pageCount = $pdf->setSourceFile($draftFullPath);

        // Determine target page for stamping with safety clamping
        $targetPage = (int) ($letter->stamp_page ?? 1);
        if ($targetPage < 1) {
            $targetPage = 1;
        } elseif ($targetPage > $pageCount) {
            $targetPage = $pageCount;
        }

        $verifyUrl = \Illuminate\Support\Facades\Route::has('verify.show')
            ? route('verify.show', ['token' => $letter->verification_token])
            : (rtrim(config('app.url', 'http://localhost:8000'), '/') . '/verify/' . $letter->verification_token);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $orientation = $size['orientation'];
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height'], true);

            // If this is the target page, stamp the QR verification badge and Cap stamp separately
            if ($pageNo === $targetPage) {
                // 1. Stamp compact QR verification badge at the bottom edge
                $qrPos = $letter->qr_position ?: ($letter->stamp_position ?: 'bottom_left');
                $this->stampQrVerificationBadge(
                    pdf: $pdf,
                    pageWidth: $size['width'],
                    pageHeight: $size['height'],
                    position: $qrPos,
                    verifyUrl: $verifyUrl,
                    token: $letter->verification_token,
                    signer: $signer
                );

                // 2. Stamp Cap Stempel BEM at custom manual coordinates
                $this->stampCapStempel(
                    pdf: $pdf,
                    pageWidth: $size['width'],
                    pageHeight: $size['height'],
                    capX: (float) ($letter->cap_x ?? 130.0),
                    capY: (float) ($letter->cap_y ?? 200.0),
                    capWidth: (float) ($letter->cap_width ?? 38.0),
                    includeSignature: (bool) ($letter->include_signature_img ?? false),
                    capPath: $capPath,
                    sigPath: $sigPath,
                    signer: $signer
                );
            }
        }

        // 7. Output final signed PDF file
        $pdf->Output($signedFullPath, 'F');

        if (!File::exists($signedFullPath)) {
            throw new Exception("Gagal membuat file PDF yang disahkan pada path: {$signedFullPath}");
        }

        // 8. Compute cryptographic SHA-256 fingerprint of the final PDF file
        $fileHash = hash_file('sha256', $signedFullPath);

        // 9. Update letter model
        $relativeSignedPath = 'signed/' . $signedFileName;
        $letter->signed_file_path = $relativeSignedPath;
        $letter->file_hash = $fileHash;
        $letter->status = 'approved';
        $letter->approved_by = $signer->id;
        $letter->approved_at = now();
        $letter->save();

        // 10. Record audit log
        AuditLogService::log('APPROVED', $letter, $signer, [
            'file_hash' => $fileHash,
            'signed_file' => $relativeSignedPath,
            'signer_name' => $signer->name,
            'signer_jabatan' => $signer->jabatan,
            'verification_token' => $letter->verification_token,
        ]);

        return [
            'success' => true,
            'signed_file_path' => $relativeSignedPath,
            'file_hash' => $fileHash,
            'approved_at' => $letter->approved_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Stamp compact QR Code verification badge at the bottom corner/edge of the PDF.
     */
    protected function stampQrVerificationBadge(
        Fpdi $pdf,
        float $pageWidth,
        float $pageHeight,
        string $position,
        string $verifyUrl,
        string $token,
        User $signer
    ): void {
        $badgeWidth = 78.0;
        $badgeHeight = 17.5;
        $marginBottom = 8.0;
        $marginSide = 12.0;

        $y = $pageHeight - $badgeHeight - $marginBottom;

        $x = match ($position) {
            'bottom_right' => $pageWidth - $badgeWidth - $marginSide,
            'bottom_center' => ($pageWidth - $badgeWidth) / 2,
            default => $marginSide, // 'bottom_left'
        };

        // Draw clean white badge with crisp border
        $pdf->SetDrawColor(203, 213, 225); // Slate 300
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetLineWidth(0.25);
        $pdf->RoundedRect($x, $y, $badgeWidth, $badgeHeight, 1.5, '1111', 'DF');

        // Deep Navy top accent bar
        $pdf->SetFillColor(15, 23, 42); // #0F172A
        $pdf->Rect($x, $y, $badgeWidth, 0.8, 'F');

        // QR Code 2D Barcode (size 14 x 14 mm)
        $qrSize = 14.0;
        $qrX = $x + 2.0;
        $qrY = $y + 1.8;

        $barcodeStyle = [
            'border' => 0,
            'vpadding' => 0,
            'hpadding' => 0,
            'fgcolor' => [15, 23, 42],
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1,
        ];
        $pdf->write2DBarcode($verifyUrl, 'QRCODE,H', $qrX, $qrY, $qrSize, $qrSize, $barcodeStyle, 'N');

        // Verification metadata text alongside QR Code
        $textX = $qrX + $qrSize + 2.5;
        $textWidth = $badgeWidth - $qrSize - 5.5;

        // Title
        $pdf->SetTextColor(15, 23, 42);
        $pdf->SetFont('helvetica', 'B', 6.2);
        $pdf->SetXY($textX, $y + 1.8);
        $pdf->Cell($textWidth, 3.0, 'BEM FIKOM E-SIGN VERIFIED', 0, 1, 'L');

        // Token code
        $shortToken = substr($token, 0, 8) . '...' . substr($token, -6);
        $pdf->SetTextColor(71, 85, 105);
        $pdf->SetFont('helvetica', '', 4.8);
        $pdf->SetXY($textX, $y + 4.8);
        $pdf->Cell($textWidth, 2.6, 'ID: ' . $shortToken . ' | X.509 & SHA-256', 0, 1, 'L');

        // Explanatory note
        $pdf->SetTextColor(100, 116, 139);
        $pdf->SetFont('helvetica', '', 4.0);
        $pdf->SetXY($textX, $y + 7.6);
        $pdf->MultiCell($textWidth, 2.4, "Scan QR untuk cek keaslian dokumen draf & daftar mahasiswa resmi yang tercantum.", 0, 'L', false, 1);

        // Signer attribution
        $pdf->SetTextColor(30, 58, 138); // #1E3A8A
        $pdf->SetFont('helvetica', 'I', 4.3);
        $pdf->SetXY($textX, $y + 13.8);
        $pdf->Cell($textWidth, 2.5, "Disahkan: {$signer->name} ({$signer->jabatan})", 0, 1, 'L');
    }

    /**
     * Stamp Cap BEM (stempel organisasi) at custom manual coordinates.
     */
    protected function stampCapStempel(
        Fpdi $pdf,
        float $pageWidth,
        float $pageHeight,
        float $capX,
        float $capY,
        float $capWidth,
        bool $includeSignature,
        string $capPath,
        string $sigPath,
        User $signer
    ): void {
        // Authentic Cap aspect ratio (1024 / 576 = 1.7777)
        $capHeight = $capWidth / 1.7777;

        // Safety clamps to ensure it doesn't spill over page bounds
        $capX = max(0.0, min($pageWidth - $capWidth, $capX));
        $capY = max(0.0, min($pageHeight - $capHeight, $capY));

        // Optional signer signature graphic underneath Cap
        if ($includeSignature && File::exists($sigPath)) {
            $sigW = $capWidth * 0.72;
            $sigH = $sigW * 0.42;
            $sigX = $capX + ($capWidth - $sigW) / 2;
            $sigY = $capY + ($capHeight - $sigH) / 2;

            $pdf->SetAlpha(0.90);
            $pdf->Image($sigPath, $sigX, $sigY, $sigW, $sigH, 'PNG', '', '', false, 300);
            $pdf->SetAlpha(1.0);
        }

        // Stamp Authentic Cap BEM FIKOM with realistic stamp transparency (alpha 0.85)
        if (File::exists($capPath)) {
            $pdf->SetAlpha(0.85);
            $pdf->Image($capPath, $capX, $capY, $capWidth, $capHeight, 'PNG', '', '', false, 300);
            $pdf->SetAlpha(1.0);
        }
    }

    /**
     * Ensure certificate files exist or generate them.
     */
    protected function ensureCertificatesExist(): void
    {
        $certPath = storage_path('app/private/certs/bem_fikom.crt');
        $keyPath = storage_path('app/private/certs/bem_fikom.key');

        if (!File::exists($certPath) || !File::exists($keyPath)) {
            Artisan::call('esign:generate-cert');
        }
    }

    /**
     * Ensure PNG assets exist or generate them.
     */
    protected function ensureAssetsExist(User $signer): void
    {
        EsignAssetGenerator::generateAll();
    }
}
