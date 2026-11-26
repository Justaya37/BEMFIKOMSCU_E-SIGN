<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePinRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SettingsController extends Controller
{
    /**
     * Display settings page for PIN, signature, and stamp assets.
     */
    public function index(): View
    {
        $user = auth()->user();
        return view('settings.index', compact('user'));
    }

    /**
     * Update signing PIN.
     */
    public function updatePin(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'current_pin' => $user->signing_pin ? ['required', 'digits:6'] : ['nullable'],
            'new_pin' => ['required', 'digits:6', 'confirmed'],
        ], [
            'current_pin.required' => 'PIN saat ini wajib dimasukkan.',
            'current_pin.digits' => 'PIN saat ini harus 6 digit.',
            'new_pin.required' => 'PIN baru wajib dimasukkan.',
            'new_pin.digits' => 'PIN baru harus tepat 6 digit angka.',
            'new_pin.confirmed' => 'Konfirmasi PIN baru tidak cocok.',
        ]);

        if ($user->signing_pin && !Hash::check($request->input('current_pin'), $user->signing_pin)) {
            return back()->with('pin_error', 'PIN lama Anda tidak sesuai.');
        }

        $user->signing_pin = Hash::make($request->input('new_pin'));
        $user->save();

        return back()->with('status', 'PIN Pengesahan 6 digit berhasil diperbarui!');
    }

    /**
     * Upload custom transparent signature PNG.
     */
    public function updateSignature(Request $request): RedirectResponse
    {
        $request->validate([
            'signature_image' => ['required', 'image', 'mimes:png', 'max:2048'],
        ], [
            'signature_image.required' => 'Pilih file gambar tanda tangan.',
            'signature_image.mimes' => 'Format file tanda tangan harus PNG dengan latar belakang transparan.',
            'signature_image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $signaturesDir = storage_path('app/private/signatures');
        File::ensureDirectoryExists($signaturesDir);

        $file = $request->file('signature_image');
        $fileName = 'sig_' . auth()->id() . '_' . Str::random(8) . '.png';
        $file->move($signaturesDir, $fileName);

        $user = auth()->user();
        $user->signature_image_path = 'signatures/' . $fileName;
        $user->save();

        return back()->with('status', 'Gambar tanda tangan digital berhasil diperbarui!');
    }

    /**
     * Upload custom transparent Cap BEM PNG.
     */
    public function updateStamp(Request $request): RedirectResponse
    {
        $request->validate([
            'stamp_image' => ['required', 'image', 'mimes:png', 'max:2048'],
        ], [
            'stamp_image.required' => 'Pilih file gambar stempel resmi.',
            'stamp_image.mimes' => 'Format file stempel harus PNG transparan.',
            'stamp_image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $assetsDir = storage_path('app/private/assets');
        File::ensureDirectoryExists($assetsDir);

        $file = $request->file('stamp_image');
        $file->move($assetsDir, 'cap_bem.png');

        return back()->with('status', 'Gambar stempel Cap BEM FIKOM berhasil diperbarui!');
    }

    /**
     * Secure preview of private asset images.
     */
    public function previewAsset(string $type): BinaryFileResponse
    {
        $user = auth()->user();

        if ($type === 'signature') {
            $path = $user->signature_image_path
                ? storage_path('app/private/' . ltrim($user->signature_image_path, '/\\'))
                : storage_path('app/private/signatures/ttd_' . ($user->isKetua() ? 'ketua' : 'wakil') . '.png');
        } elseif ($type === 'cap') {
            $path = storage_path('app/private/assets/cap_bem.png');
        } else {
            abort(404);
        }

        if (!File::exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'image/png',
        ]);
    }
}
