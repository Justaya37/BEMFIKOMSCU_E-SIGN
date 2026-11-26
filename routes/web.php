<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Protected Application Routes (Wajib Login untuk Semua Fitur)
|--------------------------------------------------------------------------
| Seluruh akses verifikasi, pengecekan surat, pengunggahan, dan arsip
| mewajibkan pengguna terotentikasi dalam sistem BEM FIKOM.
*/
Route::middleware('auth')->group(function () {
    // Root & Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Fitur Cek & Uji Integritas Dokumen
    Route::post('/verify/query', [HomeController::class, 'checkQuery'])->name('verify.query');
    Route::post('/verify/integrity', [HomeController::class, 'checkIntegrity'])->name('verify.integrity');

    // Halaman Hasil Scan QR & Download Resmi (Wajib Login)
    Route::get('/verify/{token}', [VerificationController::class, 'show'])->name('verify.show');
    Route::get('/verify/{token}/download', [VerificationController::class, 'download'])->name('verify.download');
    Route::get('/verify/{token}/preview', [VerificationController::class, 'preview'])->name('verify.preview');

    // Pengajuan & Daftar Surat (Daftar mengarah ke dashboard arsip)
    Route::get('/letters', [DashboardController::class, 'index'])->name('letters.index');
    Route::get('/letters/create', [LetterController::class, 'create'])->name('letters.create');
    Route::post('/letters', [LetterController::class, 'store'])->name('letters.store');

    // Detail & Streaming Berkas
    Route::get('/letters/{letter}', [LetterController::class, 'show'])->name('letters.show');
    Route::get('/letters/{letter}/download', [LetterController::class, 'download'])->name('letters.download');
    Route::get('/letters/{letter}/file/{type}', [LetterController::class, 'streamFile'])->name('letters.stream');

    /*
    |----------------------------------------------------------------------
    | Khusus Ketua & Wakil Ketua BEM: Persetujuan, Penolakan, Revoke, PIN & Log
    |----------------------------------------------------------------------
    */
    Route::middleware('role:ketua,wakil_ketua')->group(function () {
        Route::get('/approvals', [ApprovalController::class, 'index'])->name('approvals.index');
        Route::get('/approvals/{letter}', [ApprovalController::class, 'show'])->name('approvals.show');
        Route::post('/approvals/{letter}/sign', [ApprovalController::class, 'sign'])->name('approvals.sign');
        Route::post('/approvals/{letter}/reject', [ApprovalController::class, 'reject'])->name('approvals.reject');
        Route::post('/approvals/{letter}/revoke', [ApprovalController::class, 'revoke'])->name('approvals.revoke');

        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings/pin', [SettingsController::class, 'updatePin'])->name('settings.pin');
        Route::post('/settings/signature', [SettingsController::class, 'updateSignature'])->name('settings.signature');
        Route::post('/settings/stamp', [SettingsController::class, 'updateStamp'])->name('settings.stamp');
        Route::get('/settings/preview/{type}', [SettingsController::class, 'previewAsset'])->name('settings.preview');
    });
});
