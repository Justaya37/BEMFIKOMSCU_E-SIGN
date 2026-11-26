<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            return $user->canSign()
                ? redirect()->route('approvals.index')
                : redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login authentication.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $targetRoute = $user->canSign() ? 'approvals.index' : 'dashboard';

            return redirect()->intended(route($targetRoute))
                ->with('status', "Selamat datang kembali, {$user->name} ({$user->jabatan}).");
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Kombinasi email dan kata sandi yang Anda masukkan tidak sesuai.']);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('status', 'Anda telah keluar dari sesi sistem.');
    }
}
