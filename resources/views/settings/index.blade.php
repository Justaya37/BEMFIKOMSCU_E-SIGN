@extends('layouts.app')

@section('title', 'Pengaturan PIN & Aset Pengesahan')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ tab: 'pin' }">

    <!-- Header -->
    <div class="mb-6">
        <div class="text-xs font-mono font-bold text-blue-900 uppercase tracking-wider mb-0.5">Konfigurasi Otoritas Pengesahan</div>
        <h1 class="text-2xl font-bold text-[#0F172A]">Pengaturan PIN & Aset Digital</h1>
        <p class="text-xs text-slate-500 mt-1">
            Atur PIN keamanan pengesahan 6-digit dan kelola file tanda tangan serta cap stempel transparan resmi BEM FIKOM.
        </p>
    </div>

    <!-- Nav Tabs (Scrollable on Mobile) -->
    <div class="flex overflow-x-auto border-b border-slate-200 mb-6 bg-white card-clean rounded-t-lg">
        <button
            type="button"
            @click="tab = 'pin'"
            :class="tab === 'pin' ? 'border-b-2 border-b-[#1E3A8A] text-[#0F172A] font-bold' : 'text-slate-500 font-medium'"
            class="py-3 px-4 sm:px-6 text-xs sm:text-sm transition cursor-pointer whitespace-nowrap"
        >
            1. Atur PIN Pengesahan
        </button>

        <button
            type="button"
            @click="tab = 'signature'"
            :class="tab === 'signature' ? 'border-b-2 border-b-[#1E3A8A] text-[#0F172A] font-bold' : 'text-slate-500 font-medium'"
            class="py-3 px-4 sm:px-6 text-xs sm:text-sm transition cursor-pointer whitespace-nowrap"
        >
            2. Tanda Tangan Digital (PNG)
        </button>

        <button
            type="button"
            @click="tab = 'stamp'"
            :class="tab === 'stamp' ? 'border-b-2 border-b-[#1E3A8A] text-[#0F172A] font-bold' : 'text-slate-500 font-medium'"
            class="py-3 px-4 sm:px-6 text-xs sm:text-sm transition cursor-pointer whitespace-nowrap"
        >
            3. Cap Stempel BEM FIKOM
        </button>
    </div>

    <!-- TAB 1: PIN PENGESAHAN -->
    <div x-show="tab === 'pin'" class="card-clean rounded-b-lg p-6">
        <h2 class="text-sm font-bold text-[#0F172A] mb-1">Perbarui 6-Digit PIN Pengesahan</h2>
        <p class="text-xs text-slate-500 mb-6">
            PIN ini diperlukan setiap kali Anda menyetujui, mengesahkan, atau mencabut surat resmi untuk menjamin integritas otoritas.
        </p>

        <form action="{{ route('settings.pin') }}" method="POST" class="max-w-md space-y-4">
            @csrf

            @if($user->signing_pin)
                <div>
                    <label for="current_pin" class="block text-xs font-semibold text-slate-700 mb-1">
                        PIN Saat Ini <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="password"
                        id="current_pin"
                        name="current_pin"
                        required
                        maxlength="6"
                        pattern="\d{6}"
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-center font-mono font-bold tracking-widest text-base focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                    >
                </div>
            @endif

            <div>
                <label for="new_pin" class="block text-xs font-semibold text-slate-700 mb-1">
                    PIN Baru (Tepat 6 Digit Angka) <span class="text-rose-600">*</span>
                </label>
                <input
                    type="password"
                    id="new_pin"
                    name="new_pin"
                    required
                    maxlength="6"
                    pattern="\d{6}"
                    placeholder="Contoh: 123456"
                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-center font-mono font-bold tracking-widest text-base focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                >
            </div>

            <div>
                <label for="new_pin_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">
                    Konfirmasi PIN Baru <span class="text-rose-600">*</span>
                </label>
                <input
                    type="password"
                    id="new_pin_confirmation"
                    name="new_pin_confirmation"
                    required
                    maxlength="6"
                    pattern="\d{6}"
                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-center font-mono font-bold tracking-widest text-base focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                >
            </div>

            <button
                type="submit"
                class="px-6 py-2.5 bg-[#0F172A] hover:bg-slate-800 text-white rounded text-xs font-semibold shadow-sm transition"
            >
                Simpan PIN Baru
            </button>
        </form>
    </div>

    <!-- TAB 2: TANDA TANGAN DIGITAL -->
    <div x-show="tab === 'signature'" class="card-clean rounded-b-lg p-6">
        <h2 class="text-sm font-bold text-[#0F172A] mb-1">Gambar Tanda Tangan Transparan</h2>
        <p class="text-xs text-slate-500 mb-6">
            Goresan tanda tangan digital ini akan dicap secara presisi di atas Cap BEM pada blok pengesahan surat resmi.
        </p>

        <!-- Current Signature Preview -->
        <div class="mb-6">
            <span class="text-xs font-semibold text-slate-700 block mb-2">Pratinjau Tanda Tangan Aktif:</span>
            <div class="w-72 h-28 border border-slate-300 rounded bg-slate-50 flex items-center justify-center p-3 relative bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:8px_8px]">
                <img src="{{ route('settings.preview', ['type' => 'signature']) }}?v={{ time() }}" alt="Tanda Tangan" class="max-h-full max-w-full object-contain">
            </div>
            <span class="text-[11px] text-slate-400 mt-1 block">Tersimpan secara aman di <code class="font-mono">storage/app/private/signatures/</code></span>
        </div>

        <form action="{{ route('settings.signature') }}" method="POST" enctype="multipart/form-data" class="max-w-md space-y-4">
            @csrf
            <div>
                <label for="signature_image" class="block text-xs font-semibold text-slate-700 mb-1">
                    Unggah Gambar Tanda Tangan Baru (PNG Transparan)
                </label>
                <input
                    type="file"
                    id="signature_image"
                    name="signature_image"
                    accept="image/png"
                    required
                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#0F172A] file:text-white hover:file:bg-slate-800"
                >
                <span class="text-[11px] text-slate-400 mt-1 block">* Wajib format PNG dengan latar belakang transparan (maks 2MB).</span>
            </div>

            <button
                type="submit"
                class="px-6 py-2.5 bg-[#0F172A] hover:bg-slate-800 text-white rounded text-xs font-semibold shadow-sm transition"
            >
                Perbarui Gambar Tanda Tangan
            </button>
        </form>
    </div>

    <!-- TAB 3: CAP STEMPEL BEM -->
    <div x-show="tab === 'stamp'" class="card-clean rounded-b-lg p-6">
        <h2 class="text-sm font-bold text-[#0F172A] mb-1">Stempel Cap Resmi BEM FIKOM</h2>
        <p class="text-xs text-slate-500 mb-6">
            Gambar stempel lingkaran biru resmi BEM Fakultas Ilmu Komputer yang ditempelkan di seluruh surat digital.
        </p>

        <!-- Current Stamp Preview -->
        <div class="mb-6">
            <span class="text-xs font-semibold text-slate-700 block mb-2">Pratinjau Stempel Aktif:</span>
            <div class="w-80 h-36 border border-slate-300 rounded bg-slate-50 flex items-center justify-center p-3 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:8px_8px]">
                <img src="{{ route('settings.preview', ['type' => 'cap']) }}?v={{ time() }}" alt="Cap BEM" class="max-h-full max-w-full object-contain">
            </div>
            <span class="text-[11px] text-slate-400 mt-1 block">Tersimpan di <code class="font-mono">storage/app/private/assets/cap_bem.png</code></span>
        </div>

        <form action="{{ route('settings.stamp') }}" method="POST" enctype="multipart/form-data" class="max-w-md space-y-4">
            @csrf
            <div>
                <label for="stamp_image" class="block text-xs font-semibold text-slate-700 mb-1">
                    Unggah Stempel Cap Baru (PNG Transparan)
                </label>
                <input
                    type="file"
                    id="stamp_image"
                    name="stamp_image"
                    accept="image/png"
                    required
                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-[#0F172A] file:text-white hover:file:bg-slate-800"
                >
                <span class="text-[11px] text-slate-400 mt-1 block">* Wajib format PNG transparan berkualitas tinggi (maks 2MB).</span>
            </div>

            <button
                type="submit"
                class="px-6 py-2.5 bg-[#0F172A] hover:bg-slate-800 text-white rounded text-xs font-semibold shadow-sm transition"
            >
                Perbarui Stempel Cap BEM
            </button>
        </form>
    </div>

</div>
@endsection
