<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BEM FIKOM E-Sign') - Sistem Pengesahan & Verifikasi Surat Resmi</title>

    <!-- Official BEM FIKOM Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-bem-favicon.png') }}?v=bem1">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo-bem-favicon.png') }}?v=bem1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="/js/pdf.min.js"></script>
    @stack('styles')
</head>
<body class="min-h-screen bg-[#F8FAFC] text-slate-800 flex flex-col font-sans">

    <!-- Top Institutional Header -->
    <header class="bg-[#0F172A] text-white border-b border-slate-800 sticky top-0 z-40 shadow-sm" x-data="{ mobileDrawer: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand / Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <img src="{{ asset('images/logo_bem_fikom.png') }}" alt="Logo BEM FIKOM" class="h-10 w-auto object-contain bg-white/95 rounded px-1 py-0.5 shadow-xs">
                        <div class="border-l border-slate-700 pl-3">
                            <div class="font-bold text-sm leading-tight tracking-wide text-white flex items-center space-x-2">
                                <span>BEM FIKOM E-SIGN</span>
                                <span class="bg-blue-900/80 text-blue-300 text-[10px] px-1.5 py-0.2 rounded font-mono font-medium border border-blue-700/50">X.509</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-medium hidden sm:block">Sistem Pengesahan Surat Resmi</div>
                        </div>
                    </a>
                </div>

                <!-- Desktop Navigation (lg:flex to ensure clean layout for all roles without crowding) -->
                <nav class="hidden lg:flex items-center space-x-1 text-sm font-medium">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-md transition {{ request()->routeIs('dashboard') && !request()->hasAny(['category', 'status']) ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                            Dashboard
                        </a>

                        <!-- Dropdown Menu: Pilih & Kelola Surat -->
                        <div class="relative" x-data="{ openLettersMenu: false }" @click.outside="openLettersMenu = false">
                            <button
                                type="button"
                                @click="openLettersMenu = !openLettersMenu"
                                class="px-3 py-2 rounded-md transition flex items-center space-x-1.5 cursor-pointer {{ request()->routeIs('letters.*') || request()->hasAny(['category', 'status', 'search']) ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}"
                            >
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                <span>Pilih Surat</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': openLettersMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Box -->
                            <div
                                x-show="openLettersMenu"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute left-0 mt-2 w-64 rounded-lg bg-white text-slate-800 shadow-xl border border-slate-200 py-1.5 z-50 text-xs"
                                style="display: none;"
                            >
                                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 flex items-center justify-between">
                                    <span>Pilih Kategori Surat</span>
                                    <span class="text-blue-900 font-mono text-[9px]">Arsip</span>
                                </div>
                                <a href="{{ route('letters.index') }}#arsip-surat" @click="openLettersMenu = false" class="flex items-center space-x-2 px-3 py-2 hover:bg-slate-50 font-medium text-slate-700">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    <span>Semua Arsip Surat</span>
                                </a>
                                <a href="{{ route('letters.index', ['category' => 'surat_izin']) }}#arsip-surat" @click="openLettersMenu = false" class="flex items-center space-x-2 px-3 py-2 hover:bg-slate-50 font-medium text-slate-700">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <span>Surat Izin Kegiatan</span>
                                </a>
                                <a href="{{ route('letters.index', ['category' => 'surat_tugas']) }}#arsip-surat" @click="openLettersMenu = false" class="flex items-center space-x-2 px-3 py-2 hover:bg-slate-50 font-medium text-slate-700">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                    <span>Surat Tugas Mahasiswa</span>
                                </a>
                                <a href="{{ route('letters.index', ['category' => 'surat_undangan']) }}#arsip-surat" @click="openLettersMenu = false" class="flex items-center space-x-2 px-3 py-2 hover:bg-slate-50 font-medium text-slate-700">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    <span>Surat Undangan Resmi</span>
                                </a>
                                <a href="{{ route('letters.index', ['category' => 'surat_keterangan']) }}#arsip-surat" @click="openLettersMenu = false" class="flex items-center space-x-2 px-3 py-2 hover:bg-slate-50 font-medium text-slate-700">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    <span>Surat Keterangan Organisasi</span>
                                </a>

                                <div class="px-3 py-1.5 mt-1 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-t border-slate-100">
                                    Filter Berdasarkan Status
                                </div>
                                <a href="{{ route('letters.index', ['status' => 'pending']) }}#arsip-surat" @click="openLettersMenu = false" class="flex items-center justify-between px-3 py-2 hover:bg-amber-50/60 font-medium text-amber-900">
                                    <span class="flex items-center space-x-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        <span>Menunggu Pengesahan</span>
                                    </span>
                                </a>
                                <a href="{{ route('letters.index', ['status' => 'approved']) }}#arsip-surat" @click="openLettersMenu = false" class="flex items-center justify-between px-3 py-2 hover:bg-emerald-50/60 font-medium text-emerald-900">
                                    <span class="flex items-center space-x-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span>Sudah Disahkan (Sah)</span>
                                    </span>
                                </a>

                                <div class="border-t border-slate-100 mt-1 pt-1">
                                    <a href="{{ route('letters.create') }}" @click="openLettersMenu = false" class="flex items-center space-x-2 px-3 py-2 bg-blue-50/70 hover:bg-blue-100/70 font-semibold text-blue-900">
                                        <svg class="w-4 h-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>+ Ajukan Pengesahan Baru</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Link Spreadsheet Penomoran -->
                        <a
                            href="https://docs.google.com/spreadsheets/d/1mtOwVlwR7iU6a1cnEkySZivieXwhvJPt/edit?gid=892539536#gid=892539536"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-3 py-2 rounded-md transition flex items-center space-x-1.5 text-emerald-300 hover:text-white hover:bg-emerald-900/40 border border-emerald-600/40 group shadow-2xs"
                            title="Buka Spreadsheet Penomoran Surat Resmi BEM FIKOM"
                        >
                            <svg class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Penomoran Surat</span>
                            <span class="text-[9px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-1 py-0.2 rounded font-mono font-bold">Sheets</span>
                            <svg class="w-3 h-3 text-emerald-400/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>

                        @if(auth()->user()->canSign())
                            @php
                                $pendingApprovalsCount = \App\Models\Letter::where('status', 'pending')->count();
                            @endphp

                            <!-- Antrean Pengesahan (Utama bagi Ketua & Wakil Ketua) -->
                            <a
                                href="{{ route('approvals.index') }}"
                                class="px-3 py-2 rounded-md transition flex items-center space-x-1.5 {{ request()->routeIs('approvals.*') ? 'bg-[#1E3A8A] text-white font-bold ring-1 ring-blue-400/50' : 'text-amber-300 hover:text-white hover:bg-amber-950/40 border border-amber-500/30' }}"
                                title="Antrean pengesahan draf surat masuk"
                            >
                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                <span>Antrean Pengesahan</span>
                                @if($pendingApprovalsCount > 0)
                                    <span class="bg-amber-500 text-slate-950 text-xs px-1.5 py-0.2 rounded-full font-bold font-mono animate-pulse">{{ $pendingApprovalsCount }}</span>
                                @endif
                            </a>

                            <!-- Dropdown Khusus Kelola BEM untuk Ketua / Wakil Ketua (Log & Pengaturan) -->
                            <div class="relative" x-data="{ openAdminMenu: false }" @click.outside="openAdminMenu = false">
                                <button
                                    type="button"
                                    @click="openAdminMenu = !openAdminMenu"
                                    class="px-3 py-2 rounded-md transition flex items-center space-x-1.5 cursor-pointer {{ request()->routeIs('audit-logs.*') || request()->routeIs('settings.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}"
                                >
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>Kelola BEM</span>
                                    <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-150" :class="{ 'rotate-180': openAdminMenu }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <div
                                    x-show="openAdminMenu"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute right-0 mt-2 w-60 rounded-lg bg-white text-slate-800 shadow-xl border border-slate-200 py-1.5 z-50 text-xs"
                                    style="display: none;"
                                >
                                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                        Administrasi & Keamanan
                                    </div>
                                    <a href="{{ route('audit-logs.index') }}" @click="openAdminMenu = false" class="flex items-center space-x-2.5 px-3 py-2.5 hover:bg-slate-50 font-medium text-slate-700">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-slate-900">Log Aktivitas</div>
                                            <div class="text-[10px] text-slate-400">Riwayat audit pengesahan & aksi</div>
                                        </div>
                                    </a>
                                    <a href="{{ route('settings.index') }}" @click="openAdminMenu = false" class="flex items-center space-x-2.5 px-3 py-2.5 hover:bg-slate-50 font-medium text-slate-700 border-t border-slate-100">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        <div>
                                            <div class="font-semibold text-slate-900">Pengaturan PIN &amp; Aset</div>
                                            <div class="text-[10px] text-slate-400">Ganti PIN 6-digit &amp; berkas TTD</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endauth
                </nav>

                <!-- Auth Buttons & Desktop User Profile -->
                <div class="hidden lg:flex items-center space-x-3">
                    @auth
                        <div class="flex items-center space-x-3 border-l border-slate-700/80 pl-4">
                            <!-- Role Pill & Name -->
                            <div class="text-right">
                                <div class="text-xs font-bold text-white flex items-center justify-end space-x-1.5">
                                    <span>{{ auth()->user()->name }}</span>
                                    @if(auth()->user()->role === 'ketua')
                                        <span class="w-2 h-2 rounded-full bg-amber-400" title="Ketua BEM FIKOM"></span>
                                    @elseif(auth()->user()->role === 'wakil_ketua')
                                        <span class="w-2 h-2 rounded-full bg-blue-400" title="Wakil Ketua BEM FIKOM"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-emerald-400" title="Sekretaris BEM FIKOM"></span>
                                    @endif
                                </div>
                                <div class="text-[11px] font-medium {{ auth()->user()->canSign() ? 'text-amber-300' : 'text-blue-300' }}">
                                    {{ auth()->user()->jabatan }}
                                </div>
                            </div>

                            <!-- Logout Button -->
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" title="Keluar dari Sesi" class="p-2 rounded bg-slate-800 hover:bg-red-950/80 hover:text-red-400 text-slate-400 border border-slate-700 hover:border-red-800 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 text-xs font-semibold rounded bg-[#1E3A8A] hover:bg-blue-800 text-white border border-blue-500/40 shadow-sm transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Login Pengurus</span>
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger Button (lg:hidden) -->
                <div class="flex lg:hidden items-center space-x-2">
                    @auth
                        @if(auth()->user()->canSign())
                            @php
                                $mobilePendingCount = \App\Models\Letter::where('status', 'pending')->count();
                            @endphp
                            @if($mobilePendingCount > 0)
                                <a href="{{ route('approvals.index') }}" class="inline-flex items-center space-x-1 px-2 py-1 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-mono font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                    <span>{{ $mobilePendingCount }}</span>
                                </a>
                            @endif
                        @endif
                    @endauth

                    <button
                        @click="mobileDrawer = true"
                        type="button"
                        class="p-2 rounded-lg text-slate-300 hover:text-white hover:bg-slate-800 border border-slate-700/60 transition cursor-pointer"
                        aria-label="Buka Menu Navigasi"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Touch-First Mobile Slide-Over Drawer & Overlay (lg:hidden) -->
        <div
            x-show="mobileDrawer"
            class="fixed inset-0 z-50 lg:hidden overflow-hidden"
            style="display: none;"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop Blur -->
            <div
                x-show="mobileDrawer"
                x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="mobileDrawer = false"
                class="fixed inset-0 bg-slate-950/75 backdrop-blur-xs transition-opacity"
            ></div>

            <!-- Slide-in Drawer Container -->
            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div
                    x-show="mobileDrawer"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-sm bg-[#0F172A] border-l border-slate-800 shadow-2xl flex flex-col justify-between overflow-y-auto"
                >
                    <!-- Drawer Header -->
                    <div class="p-5 border-b border-slate-800 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('images/logo_bem_fikom.png') }}" alt="Logo BEM FIKOM" class="h-9 w-auto object-contain bg-white rounded p-0.5">
                            <div>
                                <div class="font-bold text-sm text-white flex items-center space-x-1.5">
                                    <span>BEM FIKOM E-SIGN</span>
                                    <span class="bg-blue-900 text-blue-300 text-[9px] px-1 py-0.2 rounded font-mono">X.509</span>
                                </div>
                                <div class="text-[10px] text-slate-400">Portal Pengesahan Surat Resmi</div>
                            </div>
                        </div>

                        <!-- Close Button (Min 44px touch target) -->
                        <button
                            type="button"
                            @click="mobileDrawer = false"
                            class="w-10 h-10 flex items-center justify-center rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 border border-slate-700/60 transition cursor-pointer"
                            aria-label="Tutup Menu"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Drawer Content / Menu Items -->
                    <div class="flex-grow p-4 space-y-4">
                        @auth
                            <!-- User Identity Card in Drawer -->
                            <div class="p-3.5 bg-slate-800/80 rounded-xl border border-slate-700/80 flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-[#1E3A8A] text-white flex items-center justify-center font-bold text-sm shrink-0 border border-blue-400/30">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <div class="flex-grow min-w-0">
                                    <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                                    <div class="flex items-center space-x-1.5 mt-0.5">
                                        @if(auth()->user()->role === 'ketua')
                                            <span class="bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] px-2 py-0.2 rounded-full font-semibold">Ketua BEM</span>
                                        @elseif(auth()->user()->role === 'wakil_ketua')
                                            <span class="bg-blue-500/20 text-blue-300 border border-blue-500/30 text-[10px] px-2 py-0.2 rounded-full font-semibold">Wakil Ketua BEM</span>
                                        @else
                                            <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] px-2 py-0.2 rounded-full font-semibold">Sekretaris BEM</span>
                                        @endif
                                        <span class="text-[10px] text-slate-400 font-mono">&bull; Online</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Menu Utama -->
                            <div>
                                <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Navigasi Utama
                                </div>
                                <div class="space-y-1">
                                    <a
                                        href="{{ route('dashboard') }}"
                                        @click="mobileDrawer = false"
                                        class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-xs font-semibold {{ request()->routeIs('dashboard') && !request()->hasAny(['category', 'status']) ? 'bg-[#1E3A8A] text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}"
                                    >
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        <span>Dashboard & Verifikasi</span>
                                    </a>

                                    <a
                                        href="{{ route('letters.create') }}"
                                        @click="mobileDrawer = false"
                                        class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-xs font-semibold bg-blue-950/60 hover:bg-blue-900/60 text-blue-200 border border-blue-800/40"
                                    >
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        <span>+ Ajukan Pengesahan Surat Baru</span>
                                    </a>

                                    <!-- Link Spreadsheet Penomoran -->
                                    <a
                                        href="https://docs.google.com/spreadsheets/d/1mtOwVlwR7iU6a1cnEkySZivieXwhvJPt/edit?gid=892539536#gid=892539536"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-semibold bg-emerald-950/50 hover:bg-emerald-900/50 text-emerald-200 border border-emerald-800/40"
                                    >
                                        <div class="flex items-center space-x-3">
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>Buku Penomoran (Sheets)</span>
                                        </div>
                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Section: Pilih & Arsip Surat -->
                            <div>
                                <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Pilih & Kategori Surat
                                </div>
                                <div class="space-y-1">
                                    <a
                                        href="{{ route('letters.index') }}#arsip-surat"
                                        @click="mobileDrawer = false"
                                        class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium text-slate-300 hover:text-white hover:bg-slate-800"
                                    >
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                        <span>Semua Arsip Surat</span>
                                    </a>
                                    <a
                                        href="{{ route('letters.index', ['status' => 'pending']) }}#arsip-surat"
                                        @click="mobileDrawer = false"
                                        class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium text-amber-300 hover:text-white hover:bg-slate-800"
                                    >
                                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                        <span>Menunggu Pengesahan</span>
                                    </a>
                                    <a
                                        href="{{ route('letters.index', ['status' => 'approved']) }}#arsip-surat"
                                        @click="mobileDrawer = false"
                                        class="flex items-center space-x-3 px-3 py-2 rounded-lg text-xs font-medium text-emerald-300 hover:text-white hover:bg-slate-800"
                                    >
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span>Surat Sah / Terverifikasi</span>
                                    </a>
                                </div>
                            </div>

                            <!-- Section Khusus: Otoritas Ketua / Wakil Ketua BEM -->
                            @if(auth()->user()->canSign())
                                <div>
                                    <div class="px-2 pb-1.5 text-[10px] font-bold uppercase tracking-wider text-amber-400/90 flex items-center justify-between">
                                        <span>Otoritas Ketua / Wakil</span>
                                        <span class="font-mono text-[9px] bg-amber-500/20 text-amber-300 px-1 py-0.2 rounded">PIN X.509</span>
                                    </div>
                                    <div class="space-y-1">
                                        @php
                                            $drawerPendingCount = \App\Models\Letter::where('status', 'pending')->count();
                                        @endphp
                                        <a
                                            href="{{ route('approvals.index') }}"
                                            @click="mobileDrawer = false"
                                            class="flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-bold {{ request()->routeIs('approvals.*') ? 'bg-[#1E3A8A] text-white' : 'bg-amber-950/40 text-amber-200 border border-amber-600/30 hover:bg-amber-900/50' }}"
                                        >
                                            <div class="flex items-center space-x-3">
                                                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                <span>Antrean Pengesahan</span>
                                            </div>
                                            @if($drawerPendingCount > 0)
                                                <span class="bg-amber-500 text-slate-950 text-xs px-2 py-0.5 rounded-full font-bold font-mono">{{ $drawerPendingCount }} Menunggu</span>
                                            @else
                                                <span class="text-[10px] text-slate-400 font-normal">Bersih</span>
                                            @endif
                                        </a>

                                        <a
                                            href="{{ route('audit-logs.index') }}"
                                            @click="mobileDrawer = false"
                                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-xs font-medium {{ request()->routeIs('audit-logs.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}"
                                        >
                                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span>Log Aktivitas Digital</span>
                                        </a>

                                        <a
                                            href="{{ route('settings.index') }}"
                                            @click="mobileDrawer = false"
                                            class="flex items-center space-x-3 px-3 py-2.5 rounded-lg text-xs font-medium {{ request()->routeIs('settings.*') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-300 hover:text-white hover:bg-slate-800' }}"
                                        >
                                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            <span>Pengaturan PIN & Aset TTD</span>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="p-4 bg-slate-800/80 rounded-xl text-center space-y-3">
                                <p class="text-xs text-slate-300">Masuk untuk mengunggah draf surat atau mengesahkan dokumen resmi BEM FIKOM.</p>
                                <a
                                    href="{{ route('login') }}"
                                    class="block w-full py-2.5 px-4 rounded-lg bg-[#1E3A8A] hover:bg-blue-800 text-white text-xs font-bold shadow-md transition"
                                >
                                    Login Pengurus BEM
                                </a>
                            </div>
                        @endauth
                    </div>

                    <!-- Drawer Footer / Logout Action -->
                    @auth
                        <div class="p-4 border-t border-slate-800 bg-slate-900/60">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 rounded-lg bg-red-950/60 hover:bg-red-900/60 text-red-300 border border-red-800/50 text-xs font-bold transition cursor-pointer min-h-[44px]"
                                >
                                    <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Keluar dari Sesi (Logout)</span>
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow pb-20 sm:pb-16">
        <!-- Global Notifications & Flash Alerts -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            @if(session('status'))
                <div class="mb-4 bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded text-sm flex items-start space-x-2">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded text-sm flex items-start space-x-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('pin_error'))
                <div class="mb-4 bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded text-sm flex items-start space-x-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div><b>KESALAHAN PIN:</b> {{ session('pin_error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded text-sm">
                    <div class="font-semibold mb-1">Periksa kembali data yang dimasukkan:</div>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Fixed Institutional Footer -->
    <footer class="fixed bottom-0 left-0 right-0 z-40 bg-[#0F172A]/95 backdrop-blur-md border-t border-slate-800 text-slate-300 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-14 sm:h-12 text-xs">
                <!-- Left: BEM FIKOM & Instagram Pill -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="hidden md:flex items-center space-x-2 border-r border-slate-700/80 pr-3">
                        <span class="font-bold text-white tracking-wide text-xs">BEM FIKOM SCU</span>
                        <span class="text-slate-500">&bull;</span>
                        <span class="text-[11px] text-slate-400">E-Sign Portal</span>
                    </div>

                    <!-- Instagram Link Pill -->
                    <a
                        href="https://www.instagram.com/bemfikomscu/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full bg-slate-800/90 hover:bg-slate-700/90 border border-slate-700 hover:border-pink-500/60 text-white transition-all shadow-xs group"
                        title="Kunjungi Instagram Resmi BEM FIKOM SCU (@bemfikomscu)"
                    >
                        <!-- Official Instagram Icon -->
                        <svg class="w-3.5 h-3.5 text-pink-400 group-hover:scale-110 transition-transform shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        <span class="font-mono text-[11px] font-bold text-slate-100 group-hover:text-pink-300">@bemfikomscu</span>
                    </a>

                    <!-- Spreadsheet Quick Link on Footer -->
                    <a
                        href="https://docs.google.com/spreadsheets/d/1mtOwVlwR7iU6a1cnEkySZivieXwhvJPt/edit?gid=892539536#gid=892539536"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden sm:inline-flex items-center space-x-1 text-[11px] text-emerald-400 hover:text-emerald-300 transition"
                    >
                        <span>Buku Nomor Surat</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>

                <!-- Right: Cryptography standard & System Status -->
                <div class="flex items-center space-x-3 text-[11px]">
                    <div class="hidden lg:flex items-center space-x-2 text-slate-400 font-mono">
                        <span>X.509 PKCS#7</span>
                        <span>&bull;</span>
                        <span>SHA-256 Integritas</span>
                    </div>

                    <div class="flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-emerald-950/80 border border-emerald-700/60 text-emerald-300 text-[10px] font-mono font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="hidden sm:inline">Sistem Aktif & Terverifikasi</span>
                        <span class="sm:hidden">Aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
