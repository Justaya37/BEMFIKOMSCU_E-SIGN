<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Letter;
use App\Models\LetterRecipient;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\EsignAssetGenerator;
use App\Services\PdfSigningService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ensure Certificates and PNG Assets are generated
        Artisan::call('esign:generate-cert');
        EsignAssetGenerator::generateAll();

        // 2. Create the 3 Standard Demo Accounts
        $ketua = User::updateOrCreate(
            ['email' => 'ketua@bemfikom.ac.id'],
            [
                'name' => 'M. Fikri Al-Habsyi',
                'password' => Hash::make('password123'),
                'role' => 'ketua',
                'jabatan' => 'Ketua BEM FIKOM',
                'signing_pin' => Hash::make('123456'),
                'signature_image_path' => 'signatures/ttd_ketua.png',
                'email_verified_at' => now(),
            ]
        );

        $wakil = User::updateOrCreate(
            ['email' => 'wakil@bemfikom.ac.id'],
            [
                'name' => 'Dina Permata Sari',
                'password' => Hash::make('password123'),
                'role' => 'wakil_ketua',
                'jabatan' => 'Wakil Ketua BEM FIKOM',
                'signing_pin' => Hash::make('654321'),
                'signature_image_path' => 'signatures/ttd_wakil.png',
                'email_verified_at' => now(),
            ]
        );

        $sekretaris = User::updateOrCreate(
            ['email' => 'sekretaris@bemfikom.ac.id'],
            [
                'name' => 'Rizal Pratama',
                'password' => Hash::make('password123'),
                'role' => 'sekretaris',
                'jabatan' => 'Sekretaris BEM FIKOM',
                'signing_pin' => null,
                'signature_image_path' => null,
                'email_verified_at' => now(),
            ]
        );

        $signingService = new PdfSigningService();

        // ---------------------------------------------------------------------
        // 3. Sample Letter 1: SAH / APPROVED
        // ---------------------------------------------------------------------
        $draft1Path = 'drafts/draft_014_hackathon.pdf';
        File::copy(storage_path('app/private/drafts/sample_draft.pdf'), storage_path('app/private/' . $draft1Path));

        $letter1 = Letter::updateOrCreate(
            ['letter_number' => '014/BEM-FIKOM/XI/2026'],
            [
                'verification_token' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
                'title' => 'Permohonan Dispensasi & Izin Mengikuti Lomba Hackathon Nasional',
                'category' => 'surat_izin',
                'letter_date' => now()->subDays(2),
                'event_name' => 'National Hackathon & Cyber Security Innovation 2026',
                'draft_file_path' => $draft1Path,
                'status' => 'pending',
                'submitted_by' => $sekretaris->id,
                'stamp_page' => 1,
                'stamp_position' => 'bottom_right',
                'qr_position' => 'bottom_left',
                'cap_x' => 125.0,
                'cap_y' => 195.0,
                'cap_width' => 38.0,
                'include_signature_img' => false,
            ]
        );

        // Recipients
        $recipients1 = [
            ['student_name' => 'Ahmad Fauzi Rahman', 'nim' => '2310511045', 'notes' => 'Ketua Tim Delegasi'],
            ['student_name' => 'Siti Nurhaliza Putri', 'nim' => '2310511082', 'notes' => 'Anggota / Backend Dev'],
            ['student_name' => 'Budi Santoso Wibowo', 'nim' => '2310511099', 'notes' => 'Anggota / UI-UX Designer'],
        ];

        foreach ($recipients1 as $r) {
            LetterRecipient::updateOrCreate(
                ['letter_id' => $letter1->id, 'nim' => $r['nim']],
                $r
            );
        }

        AuditLogService::log('SUBMITTED', $letter1, $sekretaris, [
            'letter_number' => $letter1->letter_number,
            'recipients_count' => 3,
        ]);

        // Execute signing via service for Letter 1
        $signingService->signLetter($letter1, $ketua);

        // ---------------------------------------------------------------------
        // 4. Sample Letter 2: MENUNGGU / PENDING (For Reviewing & Signing Demo)
        // ---------------------------------------------------------------------
        $draft2Path = 'drafts/draft_015_seminar.pdf';
        File::copy(storage_path('app/private/drafts/sample_draft.pdf'), storage_path('app/private/' . $draft2Path));

        $letter2 = Letter::updateOrCreate(
            ['letter_number' => '015/BEM-FIKOM/XI/2026'],
            [
                'verification_token' => 'b2c3d4e5-f6a7-8901-bcde-f23456789012',
                'title' => 'Surat Tugas Kepanitiaan Seminar Nasional Artificial Intelligence 2026',
                'category' => 'surat_tugas',
                'letter_date' => now(),
                'event_name' => 'Seminar & Workshop AI Era 5.0 Nasional',
                'draft_file_path' => $draft2Path,
                'status' => 'pending',
                'submitted_by' => $sekretaris->id,
                'stamp_page' => 1,
                'stamp_position' => 'bottom_right',
                'qr_position' => 'bottom_left',
                'cap_x' => 125.0,
                'cap_y' => 195.0,
                'cap_width' => 38.0,
                'include_signature_img' => false,
            ]
        );

        $recipients2 = [
            ['student_name' => 'Kevin Sanjaya', 'nim' => '2210511005', 'notes' => 'Ketua Pelaksana'],
            ['student_name' => 'Amanda Gabriella', 'nim' => '2310511019', 'notes' => 'Divisi Acara & Narasumber'],
        ];

        foreach ($recipients2 as $r) {
            LetterRecipient::updateOrCreate(
                ['letter_id' => $letter2->id, 'nim' => $r['nim']],
                $r
            );
        }

        AuditLogService::log('SUBMITTED', $letter2, $sekretaris, [
            'letter_number' => $letter2->letter_number,
            'recipients_count' => 2,
        ]);

        // ---------------------------------------------------------------------
        // 5. Sample Letter 3: DICABUT / REVOKED
        // ---------------------------------------------------------------------
        $draft3Path = 'drafts/draft_008_exchange.pdf';
        File::copy(storage_path('app/private/drafts/sample_draft.pdf'), storage_path('app/private/' . $draft3Path));

        $letter3 = Letter::updateOrCreate(
            ['letter_number' => '008/BEM-FIKOM/X/2026'],
            [
                'verification_token' => 'c3d4e5f6-a7b8-9012-cdef-345678901234',
                'title' => 'Surat Rekomendasi Pendelegasian Pertukaran Mahasiswa Internasional',
                'category' => 'surat_keterangan',
                'letter_date' => now()->subWeeks(2),
                'event_name' => 'International Student Leadership & Exchange Forum',
                'draft_file_path' => $draft3Path,
                'status' => 'pending',
                'submitted_by' => $sekretaris->id,
                'stamp_page' => 1,
                'stamp_position' => 'bottom_right',
                'qr_position' => 'bottom_left',
                'cap_x' => 125.0,
                'cap_y' => 195.0,
                'cap_width' => 38.0,
                'include_signature_img' => false,
            ]
        );

        LetterRecipient::updateOrCreate(
            ['letter_id' => $letter3->id, 'nim' => '2110511030'],
            [
                'letter_id' => $letter3->id,
                'student_name' => 'Rangga Aditya Pratama',
                'nim' => '2110511030',
                'notes' => 'Mahasiswa Delegasi',
            ]
        );

        // First sign it
        $signingService->signLetter($letter3, $wakil);

        // Then revoke it
        $revokeReason = 'Pengesahan surat dicabut karena adanya temuan ketidaksesuaian berkas akademik mahasiswa bersangkutan.';
        $letter3->update([
            'status' => 'revoked',
            'rejection_or_revoke_reason' => $revokeReason,
        ]);

        AuditLogService::log('REVOKED', $letter3, $wakil, [
            'reason' => $revokeReason,
            'revoked_by' => $wakil->name,
            'jabatan' => $wakil->jabatan,
        ]);

        // Seed some sample scan and verification audit logs
        AuditLogService::log('VERIFIED_QR', $letter1, null, [
            'source' => 'qr_scanner',
            'letter_number' => $letter1->letter_number,
            'status' => 'approved',
        ]);

        AuditLogService::log('VERIFIED_HASH_MATCH', $letter1, null, [
            'computed_hash' => $letter1->file_hash,
            'status' => 'approved',
        ]);
    }
}
