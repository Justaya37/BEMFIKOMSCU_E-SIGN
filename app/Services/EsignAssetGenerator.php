<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use TCPDF;

class EsignAssetGenerator
{
    /**
     * Generate all demo assets if they do not exist.
     */
    public static function generateAll(bool $force = false): void
    {
        $assetsDir = storage_path('app/private/assets');
        $signaturesDir = storage_path('app/private/signatures');
        $draftsDir = storage_path('app/private/drafts');

        File::ensureDirectoryExists($assetsDir);
        File::ensureDirectoryExists($signaturesDir);
        File::ensureDirectoryExists($draftsDir);

        $capPath = $assetsDir . '/cap_bem.png';
        if (!File::exists($capPath)) {
            self::generateCapBem($capPath);
        }

        $ttdKetuaPath = $signaturesDir . '/ttd_ketua.png';
        if (!File::exists($ttdKetuaPath) || $force) {
            self::generateSignature($ttdKetuaPath, 'M. Fikri Al-Habsyi', 'Ketua BEM');
        }

        $ttdWakilPath = $signaturesDir . '/ttd_wakil.png';
        if (!File::exists($ttdWakilPath) || $force) {
            self::generateSignature($ttdWakilPath, 'Dina Permata Sari', 'Wakil Ketua BEM');
        }

        $draftPath = $draftsDir . '/sample_draft.pdf';
        if (!File::exists($draftPath) || $force) {
            self::generateSampleDraftPdf($draftPath);
        }
    }

    /**
     * Generate an authentic circular BEM stamp PNG with transparent background.
     */
    public static function generateCapBem(string $targetPath): void
    {
        $size = 400;
        $img = imagecreatetruecolor($size, $size);

        // Enable alpha blending and save alpha
        imagealphablending($img, false);
        imagesavealpha($img, true);

        // Transparent background
        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefilledrectangle($img, 0, 0, $size, $size, $transparent);

        imagealphablending($img, true);

        // Navy Blue Ink (#1E3A8A) with slight ink-stamp feel (opacity ~88%)
        $navy = imagecolorallocatealpha($img, 30, 58, 138, 15);
        $navyLight = imagecolorallocatealpha($img, 30, 58, 138, 30);

        imagesetthickness($img, 6);

        // Outer circle
        imageellipse($img, 200, 200, 360, 360, $navy);

        // Inner circle
        imagesetthickness($img, 3);
        imageellipse($img, 200, 200, 340, 340, $navy);

        // Center circle
        imagesetthickness($img, 2);
        imageellipse($img, 200, 200, 230, 230, $navyLight);

        // Central text
        // Fallback font using built-in imagestring if TTF is not bundled
        $font = 5; // Built-in PHP GD font 5 (large)
        $text1 = "BEM FIKOM";
        $text2 = "PENGESAHAN";
        $text3 = "RESMI DOKUMEN";
        $text4 = "* UNIVERSITAS *";

        // Center calculation
        $w1 = imagefontwidth($font) * strlen($text1);
        $w2 = imagefontwidth($font) * strlen($text2);
        $w3 = imagefontwidth($font) * strlen($text3);
        $w4 = imagefontwidth(3) * strlen($text4);

        imagestring($img, $font, (int)(200 - $w1 / 2), 160, $text1, $navy);
        imagestring($img, 4, (int)(200 - $w2 / 2), 195, $text2, $navy);
        imagestring($img, 3, (int)(200 - $w3 / 2), 220, $text3, $navy);

        // Curved / top & bottom border indicators
        imagestring($img, 3, (int)(200 - $w4 / 2), 70, $text4, $navy);
        $bottomText = "- FAKULTAS ILMU KOMPUTER -";
        $wB = imagefontwidth(3) * strlen($bottomText);
        imagestring($img, 3, (int)(200 - $wB / 2), 310, $bottomText, $navy);

        // Decorative stars
        imagestring($img, 5, 40, 192, "*", $navy);
        imagestring($img, 5, 348, 192, "*", $navy);

        imagepng($img, $targetPath);
        imagedestroy($img);
    }

    /**
     * Generate an authentic cursive signature PNG with transparent background.
     */
    public static function generateSignature(string $targetPath, string $name, string $title): void
    {
        $w = 400;
        $h = 160;
        $img = imagecreatetruecolor($w, $h);

        imagealphablending($img, false);
        imagesavealpha($img, true);

        $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
        imagefilledrectangle($img, 0, 0, $w, $h, $transparent);

        imagealphablending($img, true);

        // Signature ink color: Deep Dark Navy (#0F172A)
        $ink = imagecolorallocatealpha($img, 15, 23, 42, 10);
        $inkLight = imagecolorallocatealpha($img, 30, 58, 138, 40);

        imagesetthickness($img, 3);

        // Draw realistic signature curve coordinates
        $points = [
            [50, 95], [65, 45], [80, 115], [95, 60], [110, 85],
            [125, 75], [140, 90], [160, 65], [180, 105], [200, 70],
            [220, 85], [240, 60], [270, 95], [310, 50], [330, 90],
            [350, 80], [360, 85]
        ];

        // Draw smooth splines/lines between points
        for ($i = 0; $i < count($points) - 1; $i++) {
            imageline($img, $points[$i][0], $points[$i][1], $points[$i+1][0], $points[$i+1][1], $ink);
        }

        // Draw elegant underline flourish
        imagesetthickness($img, 2);
        imageline($img, 45, 118, 350, 112, $ink);
        imageline($img, 280, 112, 365, 105, $ink);

        // Small initial loop
        imagearc($img, 65, 55, 30, 40, 0, 360, $ink);
        imagearc($img, 170, 75, 40, 25, 30, 280, $inkLight);

        imagepng($img, $targetPath);
        imagedestroy($img);
    }

    /**
     * Generate a professional BEM FIKOM official letter PDF draft.
     */
    public static function generateSampleDraftPdf(string $targetPath): void
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Document metadata
        $pdf->SetCreator('BEM FIKOM E-Sign');
        $pdf->SetAuthor('BEM Fakultas Ilmu Komputer');
        $pdf->SetTitle('Draf Surat Resmi BEM FIKOM');

        // Remove default header/footer for custom institutional layout
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(20, 20, 20);
        $pdf->SetAutoPageBreak(true, 25);

        $pdf->AddPage();

        $logoPath = storage_path('app/private/assets/logo_bem_fikom.png');
        if (File::exists($logoPath)) {
            $pdf->Image($logoPath, 20, 18, 55, 0, 'PNG');
        }

        $html = <<<HTML
        <table width="100%" cellpadding="2" style="border-bottom: 3px double #0F172A;">
            <tr>
                <td width="35%"></td>
                <td width="65%" align="right">
                    <span style="font-size: 13pt; font-weight: bold; color: #0F172A;">BADAN EKSEKUTIF MAHASISWA</span><br/>
                    <span style="font-size: 14pt; font-weight: bold; color: #1E3A8A;">FAKULTAS ILMU KOMPUTER</span><br/>
                    <span style="font-size: 8.5pt; color: #475569;">Gedung Graha Mahasiswa Lt. 2 &bull; Surel: sekretariat@bemfikom.ac.id</span>
                </td>
            </tr>
        </table>

        <br/><br/>
        <table width="100%" cellpadding="2" style="font-size: 10pt; color: #0F172A;">
            <tr>
                <td width="18%">Nomor Surat</td>
                <td width="2%">:</td>
                <td width="45%"><b>014/BEM-FIKOM/XI/2026</b></td>
                <td width="35%" align="right">Jakarta, 25 November 2026</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td>1 (satu) Berkas Delegasi</td>
                <td></td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td><b>Permohonan Dispensasi & Izin Mengikuti Lomba Hackathon Nasional</b></td>
                <td></td>
            </tr>
        </table>

        <br/><br/>
        <p style="font-size: 10pt; line-height: 1.5; color: #0F172A; text-align: justify;">
            Kepada Yth.<br/>
            <b>Bapak/Ibu Dosen Pengampu Mata Kuliah</b><br/>
            Fakultas Ilmu Komputer<br/>
            di Tempat
        </p>

        <p style="font-size: 10pt; line-height: 1.6; color: #0F172A; text-align: justify;">
            Dengan hormat,<br/>
            Sehubungan dengan keikutsertaan delegasi mahasiswa Fakultas Ilmu Komputer dalam kegiatan <b>National Hackathon & Cyber Security Innovation 2026</b> yang diselenggarakan pada tanggal 28-30 November 2026, maka dengan ini Badan Eksekutif Mahasiswa Fakultas Ilmu Komputer memohon dispensasi dan izin perkuliahan bagi mahasiswa/i berikut:
        </p>

        <table width="100%" border="1" cellpadding="5" cellspacing="0" style="font-size: 9pt; border-collapse: collapse; border-color: #CBD5E1;">
            <tr style="background-color: #F1F5F9; font-weight: bold; color: #0F172A;">
                <th width="8%" align="center">No</th>
                <th width="42%">Nama Lengkap Mahasiswa</th>
                <th width="25%">NIM</th>
                <th width="25%">Keterangan / Peran</th>
            </tr>
            <tr>
                <td align="center">1</td>
                <td><b>Ahmad Fauzi Rahman</b></td>
                <td><code>2310511045</code></td>
                <td>Ketua Tim Delegasi</td>
            </tr>
            <tr>
                <td align="center">2</td>
                <td><b>Siti Nurhaliza Putri</b></td>
                <td><code>2310511082</code></td>
                <td>Anggota / Backend Dev</td>
            </tr>
            <tr>
                <td align="center">3</td>
                <td><b>Budi Santoso Wibowo</b></td>
                <td><code>2310511099</code></td>
                <td>Anggota / UI-UX Designer</td>
            </tr>
        </table>

        <p style="font-size: 10pt; line-height: 1.6; color: #0F172A; text-align: justify;">
            Demikian surat permohonan ini kami sampaikan. Atas perhatian, dukungan, dan kerja sama Bapak/Ibu Dosen, kami mengucapkan terima kasih.
        </p>

        <br/>
        <table width="100%" cellpadding="2">
            <tr>
                <td width="55%"></td>
                <td width="45%" align="center" style="font-size: 10pt; color: #0F172A;">
                    Pengurus BEM FIKOM,<br/>
                    <b>Ketua BEM Fakultas Ilmu Komputer</b>
                    <br/><br/><br/><br/><br/>
                    <u><b>M. Fikri Al-Habsyi</b></u><br/>
                    NIM. 2210511012
                </td>
            </tr>
        </table>
HTML;

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output($targetPath, 'F');
    }
}
