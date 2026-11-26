<?php

namespace Tests\Feature;

use App\Models\Letter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class EsignWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_is_redirected_to_login_portal(): void
    {
        // Visiting root as guest redirects to /login
        $response = $this->get('/');
        $response->assertRedirect('/login');

        // Login page loads with official branding
        $loginPage = $this->get('/login');
        $loginPage->assertStatus(200);
        $loginPage->assertSee('Portal Pengesahan Surat & Kriptografi Digital', false);
        $loginPage->assertSee('Logo BEM Fakultas Ilmu Komputer');
        $loginPage->assertSee('Akses Terproteksi');
    }

    public function test_authenticated_user_can_access_letters_create_form(): void
    {
        $sekretaris = User::where('role', 'sekretaris')->first();
        $this->actingAs($sekretaris);

        $response = $this->get('/letters/create');
        $response->assertStatus(200);
        $response->assertSee('Ajukan Pengesahan Surat Resmi Baru');
        $response->assertSee('Nomor Surat Resmi');
        $response->assertSee('File Draf PDF Asli');
    }

    public function test_authenticated_user_accesses_dashboard_with_verification_hub(): void
    {
        $sekretaris = User::where('role', 'sekretaris')->first();
        $this->actingAs($sekretaris);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('BEM FIKOM E-SIGN');
        $response->assertSee('Dashboard Pengurus & Portal Verifikasi Dokumen', false);
        $response->assertSee('Cek Nomor / Token Surat');
        $response->assertSee('Uji Integritas File PDF (Anti-Edit SHA-256)');
    }

    public function test_guest_accessing_verify_is_redirected_to_login(): void
    {
        $letter = Letter::where('status', 'approved')->first();
        $this->assertNotNull($letter);

        // Guest must be redirected to login
        $response = $this->get('/verify/' . $letter->verification_token);
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_verify_letter_qr(): void
    {
        $sekretaris = User::where('role', 'sekretaris')->first();
        $this->actingAs($sekretaris);

        $letter = Letter::where('status', 'approved')->first();
        $this->assertNotNull($letter);

        $response = $this->get('/verify/' . $letter->verification_token);
        $response->assertStatus(200);
        $response->assertSee('SURAT RESMI SAH & TERDAFTAR', false);
        $response->assertSee($letter->letter_number);
        $response->assertSee($letter->file_hash);
    }

    public function test_integrity_check_validates_real_pdf_and_detects_fake(): void
    {
        $user = User::where('role', 'sekretaris')->first();
        $this->actingAs($user);

        $approvedLetter = Letter::where('status', 'approved')->first();
        $this->assertNotNull($approvedLetter);

        $realPdfPath = storage_path('app/private/' . $approvedLetter->signed_file_path);
        $this->assertTrue(File::exists($realPdfPath));

        // 1. Test Match with real PDF
        $realUploadedFile = new UploadedFile(
            $realPdfPath,
            'real_letter.pdf',
            'application/pdf',
            null,
            true
        );

        $responseMatch = $this->postJson('/verify/integrity', [
            'pdf_file' => $realUploadedFile,
        ]);

        $responseMatch->assertStatus(200);
        $responseMatch->assertJson([
            'match' => true,
            'status_title' => 'DOKUMEN ASLI & TERVERIFIKASI (100% UTUH)',
        ]);

        // 2. Test Mismatch with modified/fake PDF
        $fakePdf = UploadedFile::fake()->create('fake_modified.pdf', 500, 'application/pdf');

        $responseMismatch = $this->postJson('/verify/integrity', [
            'pdf_file' => $fakePdf,
        ]);

        $responseMismatch->assertStatus(422);
        $responseMismatch->assertJson([
            'match' => false,
            'status_title' => 'DOKUMEN TIDAK TERDAFTAR ATAU TELAH DIMODIFIKASI',
        ]);
    }

    public function test_role_security_sekretaris_cannot_access_approvals_or_audit_logs(): void
    {
        $sekretaris = User::where('role', 'sekretaris')->first();
        $this->actingAs($sekretaris);

        // Sekretaris trying to access approvals index -> 403 Forbidden
        $response = $this->get('/approvals');
        $response->assertStatus(403);

        // Sekretaris trying to access audit logs -> 403 Forbidden
        $responseLogs = $this->get('/audit-logs');
        $responseLogs->assertStatus(403);
    }

    public function test_ketua_can_sign_pending_letter_with_valid_pin(): void
    {
        $ketua = User::where('role', 'ketua')->first();
        $pendingLetter = Letter::where('status', 'pending')->first();
        $this->assertNotNull($pendingLetter);

        $this->actingAs($ketua);

        // 1. Wrong PIN fails
        $wrongPinResponse = $this->post('/approvals/' . $pendingLetter->id . '/sign', [
            'signing_pin' => '999999',
        ]);
        $wrongPinResponse->assertSessionHas('pin_error');

        $pendingLetter->refresh();
        $this->assertEquals('pending', $pendingLetter->status);

        // 2. Correct PIN 123456 succeeds
        $correctPinResponse = $this->post('/approvals/' . $pendingLetter->id . '/sign', [
            'signing_pin' => '123456',
        ]);
        $correctPinResponse->assertSessionHas('status');

        $pendingLetter->refresh();
        $this->assertEquals('approved', $pendingLetter->status);
        $this->assertNotNull($pendingLetter->signed_file_path);
        $this->assertNotNull($pendingLetter->file_hash);
        $this->assertEquals(64, strlen($pendingLetter->file_hash));
        $this->assertEquals($ketua->id, $pendingLetter->approved_by);
    }

    public function test_ketua_can_revoke_approved_letter(): void
    {
        $ketua = User::where('role', 'ketua')->first();
        $approvedLetter = Letter::where('status', 'approved')->first();
        $this->actingAs($ketua);

        $response = $this->post('/approvals/' . $approvedLetter->id . '/revoke', [
            'signing_pin' => '123456',
            'reason' => 'Pencabutan pengesahan surat karena pelanggaran kode etik mahasiswa.',
        ]);

        $response->assertSessionHas('status');

        $approvedLetter->refresh();
        $this->assertEquals('revoked', $approvedLetter->status);
        $this->assertStringContainsString('pelanggaran kode etik', $approvedLetter->rejection_or_revoke_reason);
    }

    public function test_custom_cap_and_qr_positioning_flow(): void
    {
        $sekretaris = User::where('role', 'sekretaris')->first();
        $this->actingAs($sekretaris);

        $samplePdfPath = storage_path('app/private/drafts/sample_draft.pdf');
        $this->assertTrue(File::exists($samplePdfPath));

        $realUploadedPdf = new UploadedFile(
            $samplePdfPath,
            'draf_izin_kustom.pdf',
            'application/pdf',
            null,
            true
        );

        $response = $this->post('/letters', [
            'letter_number' => '099/BEM-FIKOM/XI/2026',
            'title' => 'Surat Pengajuan Custom Stamp Position',
            'category' => 'surat_izin',
            'letter_date' => '2026-11-25',
            'event_name' => 'Uji Coba Posisi Cap Manual',
            'stamp_page' => 1,
            'qr_position' => 'bottom_left',
            'cap_x' => 28.5,
            'cap_y' => 192.0,
            'cap_width' => 40.0,
            'include_signature_img' => '0',
            'draft_pdf' => $realUploadedPdf,
        ]);

        $letter = Letter::where('letter_number', '099/BEM-FIKOM/XI/2026')->first();
        $this->assertNotNull($letter);
        $this->assertEquals(28.5, $letter->cap_x);
        $this->assertEquals(192.0, $letter->cap_y);
        $this->assertEquals('bottom_left', $letter->qr_position);

        // Now Ketua signs it with custom override
        $ketua = User::where('role', 'ketua')->first();
        $this->actingAs($ketua);

        $signResponse = $this->post('/approvals/' . $letter->id . '/sign', [
            'signing_pin' => '123456',
            'cap_x' => 32.0,
            'cap_y' => 195.0,
            'cap_width' => 38.0,
            'qr_position' => 'bottom_right',
        ]);

        $signResponse->assertSessionHas('status');
        $letter->refresh();
        $this->assertEquals('approved', $letter->status);
        $this->assertEquals(32.0, $letter->cap_x);
        $this->assertEquals(195.0, $letter->cap_y);
        $this->assertEquals('bottom_right', $letter->qr_position);
        $this->assertNotNull($letter->signed_file_path);
    }

    public function test_favicon_links_are_rendered_in_layout(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('images/logo-bem-favicon.png?v=bem1', false);
        $response->assertSee('rel="icon"', false);
        $response->assertSee('rel="shortcut icon"', false);
    }

    public function test_pdf_stream_returns_proper_inline_headers_and_disposition(): void
    {
        $ketua = User::where('role', 'ketua')->first();
        $this->actingAs($ketua);

        $pendingLetter = Letter::where('status', 'pending')->first();
        $this->assertNotNull($pendingLetter);

        $response = $this->get("/letters/{$pendingLetter->id}/file/draft");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('inline;', $response->headers->get('Content-Disposition'));
    }

    public function test_stamp_page_must_be_positive_and_persists_through_approval(): void
    {
        $sekretaris = User::where('role', 'sekretaris')->first();
        $this->actingAs($sekretaris);

        $samplePdfPath = storage_path('app/private/drafts/sample_draft.pdf');
        $this->assertTrue(File::exists($samplePdfPath));

        $pdfFile = new UploadedFile(
            $samplePdfPath,
            'draf_multipage.pdf',
            'application/pdf',
            null,
            true
        );

        // 1. Negative or zero stamp_page must fail validation
        $failResponse = $this->post('/letters', [
            'letter_number' => '100/BEM-FIKOM/XI/2026',
            'title' => 'Surat Gagal Page Negatif',
            'category' => 'surat_tugas',
            'letter_date' => '2026-11-25',
            'event_name' => 'Validasi Halaman Positif',
            'stamp_page' => 0,
            'draft_pdf' => $pdfFile,
        ]);
        $failResponse->assertSessionHasErrors(['stamp_page']);

        // 2. Positive stamp_page (e.g. 2) succeeds
        $pdfFileValid = new UploadedFile(
            $samplePdfPath,
            'draf_multipage.pdf',
            'application/pdf',
            null,
            true
        );

        $successResponse = $this->post('/letters', [
            'letter_number' => '101/BEM-FIKOM/XI/2026',
            'title' => 'Surat Target Halaman 2',
            'category' => 'surat_tugas',
            'letter_date' => '2026-11-25',
            'event_name' => 'Validasi Halaman 2',
            'stamp_page' => 2,
            'qr_position' => 'bottom_left',
            'cap_x' => 45.0,
            'cap_y' => 180.0,
            'cap_width' => 38.0,
            'draft_pdf' => $pdfFileValid,
        ]);

        $letter = Letter::where('letter_number', '101/BEM-FIKOM/XI/2026')->first();
        $this->assertNotNull($letter);
        $this->assertEquals(2, $letter->stamp_page);

        // 3. Approver signs and can keep or override stamp_page
        $ketua = User::where('role', 'ketua')->first();
        $this->actingAs($ketua);

        $signResponse = $this->post('/approvals/' . $letter->id . '/sign', [
            'signing_pin' => '123456',
            'stamp_page' => 2,
            'cap_x' => 50.0,
            'cap_y' => 185.0,
            'cap_width' => 38.0,
            'qr_position' => 'bottom_right',
        ]);

        $signResponse->assertSessionHas('status');
        $letter->refresh();
        $this->assertEquals('approved', $letter->status);
        $this->assertEquals(2, $letter->stamp_page);
        $this->assertNotNull($letter->signed_file_path);
    }

    public function test_navigation_has_letter_menu_spreadsheet_and_fixed_footer_with_instagram(): void
    {
        $user = User::where('role', 'sekretaris')->first();
        $this->actingAs($user);

        // 1. Check letters.index route works
        $responseLetters = $this->get('/letters');
        $responseLetters->assertStatus(200);

        // 2. Check layout has menu Pilih Surat
        $responseLetters->assertSee('Pilih Surat');
        $responseLetters->assertSee('Semua Arsip Surat');

        // 3. Check Google Spreadsheet Penomoran link is rendered
        $responseLetters->assertSee('https://docs.google.com/spreadsheets/d/1mtOwVlwR7iU6a1cnEkySZivieXwhvJPt/edit?gid=892539536#gid=892539536', false);
        $responseLetters->assertSee('Penomoran Surat');

        // 4. Check fixed footer with Instagram @bemfikomscu
        $responseLetters->assertSee('https://www.instagram.com/bemfikomscu/', false);
        $responseLetters->assertSee('@bemfikomscu');
        $responseLetters->assertSee('BEM FIKOM SCU');
    }

    public function test_header_navigation_and_mobile_responsive_elements_for_ketua_and_wakil(): void
    {
        $ketua = User::where('role', 'ketua')->first();
        $this->actingAs($ketua);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        // 1. Check clean organized navigation for Ketua BEM
        $response->assertSee('Antrean Pengesahan');
        $response->assertSee('Kelola BEM');
        $response->assertSee('Log Aktivitas');
        $response->assertSee('Pengaturan PIN', false);
        $response->assertSee('Ketua BEM');

        // 2. Check touch-first mobile drawer elements
        $response->assertSee('mobileDrawer');
        $response->assertSee('Navigasi Utama');
        $response->assertSee('Otoritas Ketua / Wakil');
        $response->assertSee('Log Aktivitas Digital');
        $response->assertSee('Pengaturan PIN', false);

        // 3. Check approvals index has responsive mobile navigation and layout
        $responseApprovals = $this->get('/approvals');
        $responseApprovals->assertStatus(200);
        $responseApprovals->assertSee('Antrean Pengesahan Surat Masuk');
        $responseApprovals->assertSee('Otoritas Ketua / Wakil Ketua');

        // 4. Check approval review page has mobile tabs
        $letter = Letter::first();
        if ($letter) {
            $responseReview = $this->get('/approvals/' . $letter->id);
            $responseReview->assertStatus(200);
            $responseReview->assertSee('mobileWorkspaceTab');
            $responseReview->assertSee('1. Pratinjau Dokumen');
            $responseReview->assertSee('Keputusan');
        }
    }
}
