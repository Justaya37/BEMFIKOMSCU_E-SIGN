@extends('layouts.app')

@section('title', 'Portal Login & Pengesahan Surat Resmi BEM FIKOM')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="loginPortal()">
    
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <!-- LEFT COLUMN: INSTITUTIONAL BRANDING & SYSTEM OVERVIEW (7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            <div class="card-clean rounded-lg p-6 sm:p-8 bg-white border-l-4 border-l-[#1E3A8A]">
                <!-- Official Logo (File 1) -->
                <div class="mb-5">
                    <img src="{{ asset('images/logo_bem_fikom.png') }}" alt="Logo BEM Fakultas Ilmu Komputer" class="h-16 w-auto object-contain">
                </div>

                <div class="text-xs font-mono font-bold text-blue-900 uppercase tracking-wider mb-1">
                    Portal Pengesahan Surat & Kriptografi Digital
                </div>
                <h1 class="text-2xl font-bold text-[#0F172A] leading-tight">
                    Sistem Otentikasi BEM FIKOM E-Sign
                </h1>
                <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                    Sistem ini mengamankan seluruh penerbitan surat resmi organisasi mahasiswa, penandatanganan digital berbasis sertifikat X.509, pengujian sidik jari integritas SHA-256, serta verifikasi keabsahan daftar mahasiswa resmi.
                </p>

                <!-- Mandatory Login Notice -->
                <div class="mt-5 p-3.5 rounded bg-blue-50/80 border border-blue-200 text-xs text-blue-950 flex items-start space-x-2.5">
                    <svg class="w-4 h-4 text-[#1E3A8A] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <div>
                        <b>Akses Terproteksi:</b> Untuk memeriksa nomor surat, melakukan uji integritas file PDF, maupun mengesahkan draf, seluruh civitas akademika diwajibkan untuk masuk ke sistem terlebih dahulu.
                    </div>
                </div>

                <!-- 3 Pillars Features -->
                <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div class="p-3 rounded bg-slate-50 border border-slate-200">
                        <div class="font-bold text-[#0F172A]">Kriptografi X.509</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Sertifikat digital PKCS#7 standar Adobe Acrobat</div>
                    </div>
                    <div class="p-3 rounded bg-slate-50 border border-slate-200">
                        <div class="font-bold text-[#0F172A]">Sidik Jari SHA-256</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Deteksi instan file PDF yang telah dimodifikasi</div>
                    </div>
                    <div class="p-3 rounded bg-slate-50 border border-slate-200">
                        <div class="font-bold text-[#0F172A]">Stempel Resmi</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Cap resmi BEM FIKOM & 2D QR Code</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: LOGIN FORM CARD & DEMO SWITCHER (5 Cols) -->
        <div class="lg:col-span-5">
            <div class="card-clean rounded-lg p-6 sm:p-8 bg-white shadow-sm">
                <div class="mb-5">
                    <h2 class="text-base font-bold text-[#0F172A]">Masuk ke Akun Anda</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Gunakan surel resmi terdaftar untuk mengakses portal</p>
                </div>

                <!-- Fast Demo Account Selector -->
                <div class="mb-5 bg-slate-50 p-3 rounded border border-slate-200">
                    <div class="text-[11px] font-semibold text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Pilih Akun Demo (1-Klik Isi):</span>
                        <span class="text-[10px] text-blue-900 font-mono">BEM FIKOM</span>
                    </div>
                    <div class="grid grid-cols-3 gap-1.5">
                        <button
                            type="button"
                            @click="fillAccount('ketua@bemfikom.ac.id', 'password123')"
                            class="px-2 py-1.5 bg-white hover:bg-slate-100 text-slate-800 text-[11px] font-medium rounded border border-slate-300 text-center transition cursor-pointer"
                        >
                            Ketua BEM
                        </button>
                        <button
                            type="button"
                            @click="fillAccount('wakil@bemfikom.ac.id', 'password123')"
                            class="px-2 py-1.5 bg-white hover:bg-slate-100 text-slate-800 text-[11px] font-medium rounded border border-slate-300 text-center transition cursor-pointer"
                        >
                            Wakil Ketua
                        </button>
                        <button
                            type="button"
                            @click="fillAccount('sekretaris@bemfikom.ac.id', 'password123')"
                            class="px-2 py-1.5 bg-white hover:bg-slate-100 text-slate-800 text-[11px] font-medium rounded border border-slate-300 text-center transition cursor-pointer"
                        >
                            Sekretaris
                        </button>
                    </div>
                </div>

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email Resmi</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            x-model="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] transition"
                            placeholder="nama@bemfikom.ac.id"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            x-model="password"
                            required
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] transition"
                            placeholder="••••••••"
                        >
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#1E3A8A] focus:ring-[#1E3A8A]">
                            <span>Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-2.5 px-4 bg-[#0F172A] hover:bg-slate-800 text-white font-semibold text-xs rounded shadow-sm transition flex items-center justify-center space-x-1.5"
                    >
                        <span>Masuk ke Dashboard</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loginPortal() {
    return {
        email: '{{ old('email', 'sekretaris@bemfikom.ac.id') }}',
        password: '',
        fillAccount(email, password) {
            this.email = email;
            this.password = password;
        }
    };
}
</script>
@endpush
