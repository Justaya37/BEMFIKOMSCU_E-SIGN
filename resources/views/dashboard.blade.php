@extends('layouts.app')

@section('title', 'Dashboard & Verifikasi Surat Resmi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="dashboardHub()">

    <!-- 1. OFFICIAL INSTITUTIONAL HEADER & USER IDENTITY -->
    <div class="card-clean rounded-lg p-6 sm:p-8 mb-8 border-l-4 border-l-[#1E3A8A]">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start space-x-4">
                <img src="{{ asset('images/logo_bem_fikom.png') }}" alt="Logo BEM FIKOM" class="h-14 w-auto object-contain bg-white rounded p-1 border border-slate-200 shrink-0 shadow-xs">
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-blue-900 font-mono mb-1">
                        Sistem Informasi Pengesahan Surat Resmi & Kriptografi X.509
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#0F172A] leading-snug">
                        Dashboard Pengurus & Portal Verifikasi Dokumen
                    </h1>
                    <p class="text-xs text-slate-600 mt-1">
                        Selamat datang, <span class="font-bold text-slate-900">{{ auth()->user()->name }}</span> &bull;
                        <span class="text-blue-900 font-semibold">{{ auth()->user()->jabatan }}</span>
                    </p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="shrink-0 flex flex-wrap md:flex-col justify-end gap-2 border-t md:border-t-0 md:border-l border-slate-200 pt-4 md:pt-0 md:pl-6">
                <a href="{{ route('letters.create') }}" class="inline-flex items-center justify-center space-x-1.5 px-4 py-2.5 rounded bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Ajukan Surat Baru</span>
                </a>

                <a
                    href="https://docs.google.com/spreadsheets/d/1mtOwVlwR7iU6a1cnEkySZivieXwhvJPt/edit?gid=892539536#gid=892539536"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center space-x-1.5 px-4 py-2.5 rounded bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold shadow-sm transition group"
                    title="Buka Spreadsheet Buku Penomoran Surat Resmi BEM FIKOM"
                >
                    <svg class="w-4 h-4 text-emerald-300 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Buku Penomoran (Sheets)</span>
                </a>

                @if(auth()->user()->canSign())
                    <a href="{{ route('approvals.index') }}" class="inline-flex items-center justify-center space-x-1.5 px-4 py-2.5 rounded bg-[#1E3A8A] hover:bg-blue-800 text-white text-xs font-semibold shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>Antrean Pengesahan</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. STATS CARDS (4 CARDS) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="card-clean rounded-lg p-5">
            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Seluruh Surat</div>
            <div class="text-2xl font-bold font-mono text-slate-900 mt-1">{{ $stats['total'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Surat terdaftar di sistem</div>
        </div>

        <div class="card-clean rounded-lg p-5 border-l-4 border-l-amber-500">
            <div class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Menunggu Pengesahan</div>
            <div class="text-2xl font-bold font-mono text-amber-900 mt-1">{{ $stats['pending'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Perlu ditinjau Ketua/Wakil</div>
        </div>

        <div class="card-clean rounded-lg p-5 border-l-4 border-l-emerald-600">
            <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Sudah Disahkan (Sah)</div>
            <div class="text-2xl font-bold font-mono text-emerald-900 mt-1">{{ $stats['approved'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Tertanda tangan digital & QR</div>
        </div>

        <div class="card-clean rounded-lg p-5 border-l-4 border-l-rose-500">
            <div class="text-xs font-semibold text-rose-700 uppercase tracking-wider">Ditolak / Dicabut</div>
            <div class="text-2xl font-bold font-mono text-rose-900 mt-1">{{ $stats['rejected_or_revoked'] }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Tidak lagi berlaku / dibatalkan</div>
        </div>
    </div>

    <!-- 3. INTEGRATED VERIFICATION & INTEGRITY CHECK HUB -->
    <div class="card-clean rounded-lg overflow-hidden mb-8">
        <div class="flex border-b border-slate-200 bg-slate-50">
            <button
                type="button"
                @click="activeTab = 'query'"
                :class="activeTab === 'query' ? 'bg-white border-b-2 border-b-[#1E3A8A] text-[#0F172A] font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="flex-1 py-3 px-4 text-center text-xs sm:text-sm flex items-center justify-center space-x-2 transition cursor-pointer"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Cek Nomor / Token Surat</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'integrity'"
                :class="activeTab === 'integrity' ? 'bg-white border-b-2 border-b-[#1E3A8A] text-[#0F172A] font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="flex-1 py-3 px-4 text-center text-xs sm:text-sm flex items-center justify-center space-x-2 transition cursor-pointer"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Uji Integritas File PDF (Anti-Edit SHA-256)</span>
            </button>
        </div>

        <!-- TAB 1: Cek Nomor / Token Surat -->
        <div x-show="activeTab === 'query'" class="p-6">
            <h3 class="text-sm font-bold text-[#0F172A] mb-1">Pemeriksaan Surat Berdasarkan Nomor atau Token</h3>
            <p class="text-xs text-slate-500 mb-4">
                Masukkan nomor surat resmi lengkap (misal: <code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded font-mono">014/BEM-FIKOM/XI/2026</code>) atau UUID token verifikasi.
            </p>

            @if(session('query_error'))
                <div class="mb-4 bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded text-xs flex items-start space-x-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>{{ session('query_error') }}</div>
                </div>
            @endif

            <form action="{{ route('verify.query') }}" method="POST" class="max-w-2xl">
                @csrf
                <div class="flex flex-col sm:flex-row gap-2">
                    <input
                        type="text"
                        name="letter_query"
                        value="{{ old('letter_query') }}"
                        placeholder="Contoh: 014/BEM-FIKOM/XI/2026 atau Token UUID"
                        required
                        class="flex-grow px-3 py-2 bg-white border border-slate-300 rounded text-xs font-mono focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]"
                    >
                    <button
                        type="submit"
                        class="px-5 py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded text-xs font-semibold transition shrink-0"
                    >
                        Cek Dokumen
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 2: Uji Integritas File PDF -->
        <div x-show="activeTab === 'integrity'" class="p-6">
            <h3 class="text-sm font-bold text-[#0F172A] mb-1">Uji Integritas File PDF Digital (Anti-Manipulasi)</h3>
            <p class="text-xs text-slate-500 mb-4">
                Unggah file PDF surat untuk mencocokkan sidik jari SHA-256 asli dengan arsip database BEM FIKOM. File yang pernah diedit, teks diubah, atau di-crop akan langsung terdeteksi tidak sah.
            </p>

            <div
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleFileDrop($event)"
                :class="isDragging ? 'border-blue-600 bg-blue-50/50' : 'border-slate-300 bg-slate-50/70 hover:bg-slate-50'"
                class="border-2 border-dashed rounded-lg p-6 text-center transition cursor-pointer max-w-2xl"
                @click="$refs.fileInput.click()"
            >
                <input
                    type="file"
                    x-ref="fileInput"
                    @change="handleFileSelect($event)"
                    accept="application/pdf"
                    class="hidden"
                >

                <div class="flex flex-col items-center justify-center space-y-2">
                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>

                    <div class="text-xs font-semibold text-[#0F172A]">
                        Tarik & Lepas File PDF ke Sini, atau <span class="text-blue-700 underline">Pilih File</span>
                    </div>
                    <div class="text-[11px] text-slate-400">
                        Format .PDF (Maks 10MB) &bull; Komputasi Kriptografi SHA-256
                    </div>

                    <div x-show="fileName" class="text-xs font-mono bg-white px-2.5 py-1 rounded border border-slate-200 text-slate-700 font-semibold" x-text="'File: ' + fileName"></div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div x-show="isLoading" class="mt-4 text-center py-2">
                <span class="text-xs font-medium text-slate-700 animate-pulse">Menghitung Hash Kriptografi SHA-256 & Memvalidasi ke Database...</span>
            </div>

            <!-- Result Box: MATCH -->
            <div x-show="checkResult && checkResult.match" class="mt-4 card-clean rounded-lg p-4 bg-emerald-50/70 border border-emerald-300 max-w-2xl">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="flex-grow text-xs">
                        <h4 class="font-bold text-emerald-950">DOKUMEN ASLI & TERVERIFIKASI (100% UTUH)</h4>
                        <p class="text-emerald-800 mt-0.5" x-text="checkResult.message"></p>

                        <div class="mt-2 bg-white p-2.5 rounded border border-emerald-200 space-y-1">
                            <div><span class="text-slate-500">Nomor:</span> <b class="font-mono text-slate-900" x-text="checkResult.letter?.letter_number"></b></div>
                            <div><span class="text-slate-500">Perihal:</span> <span class="font-semibold text-slate-800" x-text="checkResult.letter?.title"></span></div>
                            <div><span class="text-slate-500">Disahkan Oleh:</span> <span class="font-medium text-blue-900" x-text="checkResult.letter?.approved_by + ' (' + checkResult.letter?.approved_by_jabatan + ')'"></span></div>
                            <div><span class="text-slate-500">Hash SHA-256:</span> <code class="font-mono text-[10px] text-slate-700 break-all block" x-text="checkResult.hash"></code></div>
                        </div>

                        <div class="mt-3 flex justify-end">
                            <a :href="checkResult.letter?.verify_url" class="px-3 py-1.5 rounded bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs transition">
                                Buka Halaman Verifikasi Lengkap &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Result Box: MISMATCH -->
            <div x-show="checkResult && !checkResult.match" class="mt-4 card-clean rounded-lg p-4 bg-rose-50/80 border border-rose-300 max-w-2xl">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="text-xs">
                        <h4 class="font-bold text-rose-950">DOKUMEN TIDAK TERDAFTAR ATAU TELAH DIMODIFIKASI</h4>
                        <p class="text-rose-800 mt-0.5" x-text="checkResult.message"></p>
                        <div class="mt-2 bg-white p-2 rounded border border-rose-200 font-mono text-[10px] text-rose-700 break-all" x-text="'Hash: ' + checkResult.hash"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. ARSIP SURAT RESMI TABLE -->
    <div id="arsip-surat" class="scroll-mt-20">
        <!-- Section Header with Quick Selection Pills -->
        <div class="card-clean rounded-lg p-5 mb-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 mb-3 border-b border-slate-100">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-900 border border-blue-200 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#0F172A]">Pilih & Arsip Surat Resmi</h3>
                        <p class="text-[11px] text-slate-500">Telusuri seluruh surat resmi organisasi, status pengesahan, dan dokumen sah terverifikasi</p>
                    </div>
                </div>

                <!-- Quick Category & Status Shortcut Pills (Scrollable on Mobile) -->
                <div class="flex items-center gap-1.5 text-xs overflow-x-auto pb-1.5 max-w-full">
                    <span class="text-[10px] text-slate-400 font-semibold uppercase mr-1 hidden sm:inline whitespace-nowrap">Pilih Cepat:</span>
                    <a
                        href="{{ route('dashboard') }}#arsip-surat"
                        class="px-2.5 py-1 rounded text-[11px] font-semibold transition whitespace-nowrap {{ !request()->hasAny(['category', 'status', 'search']) ? 'bg-[#0F172A] text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}"
                    >
                        Semua ({{ $stats['total'] }})
                    </a>
                    <a
                        href="{{ route('dashboard', ['status' => 'pending']) }}#arsip-surat"
                        class="px-2.5 py-1 rounded text-[11px] font-semibold transition whitespace-nowrap {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200' }}"
                    >
                        Menunggu ({{ $stats['pending'] }})
                    </a>
                    <a
                        href="{{ route('dashboard', ['status' => 'approved']) }}#arsip-surat"
                        class="px-2.5 py-1 rounded text-[11px] font-semibold transition whitespace-nowrap {{ request('status') === 'approved' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200' }}"
                    >
                        Sah ({{ $stats['approved'] }})
                    </a>
                    <a
                        href="{{ route('dashboard', ['category' => 'surat_izin']) }}#arsip-surat"
                        class="px-2.5 py-1 rounded text-[11px] font-semibold transition whitespace-nowrap {{ request('category') === 'surat_izin' ? 'bg-blue-700 text-white shadow-xs' : 'bg-blue-50 text-blue-800 hover:bg-blue-100 border border-blue-200' }}"
                    >
                        Surat Izin
                    </a>
                    <a
                        href="{{ route('dashboard', ['category' => 'surat_tugas']) }}#arsip-surat"
                        class="px-2.5 py-1 rounded text-[11px] font-semibold transition whitespace-nowrap {{ request('category') === 'surat_tugas' ? 'bg-indigo-700 text-white shadow-xs' : 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100 border border-indigo-200' }}"
                    >
                        Surat Tugas
                    </a>
                    <a
                        href="{{ route('dashboard', ['category' => 'surat_undangan']) }}#arsip-surat"
                        class="px-2.5 py-1 rounded text-[11px] font-semibold transition whitespace-nowrap {{ request('category') === 'surat_undangan' ? 'bg-purple-700 text-white shadow-xs' : 'bg-purple-50 text-purple-800 hover:bg-purple-100 border border-purple-200' }}"
                    >
                        Undangan
                    </a>
                </div>
            </div>

            <form action="{{ route('dashboard') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor surat, perihal, kegiatan, atau nama mahasiswa..."
                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]"
                >
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Pengesahan</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Sah / Disahkan</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Dicabut</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <select name="category" class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:outline-none focus:ring-2 focus:ring-[#1E3A8A]">
                    <option value="">Semua Kategori</option>
                    <option value="surat_izin" {{ request('category') === 'surat_izin' ? 'selected' : '' }}>Surat Izin</option>
                    <option value="surat_tugas" {{ request('category') === 'surat_tugas' ? 'selected' : '' }}>Surat Tugas</option>
                    <option value="surat_undangan" {{ request('category') === 'surat_undangan' ? 'selected' : '' }}>Surat Undangan</option>
                    <option value="surat_keterangan" {{ request('category') === 'surat_keterangan' ? 'selected' : '' }}>Surat Keterangan</option>
                    <option value="lainnya" {{ request('category') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded text-xs font-semibold transition shrink-0">
                    Filter
                </button>

                @if(request()->hasAny(['search', 'status', 'category']))
                    <a href="{{ route('dashboard') }}" class="p-2 text-slate-500 hover:text-slate-800 text-xs shrink-0" title="Reset filter">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-clean rounded-lg overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                <thead class="bg-slate-50 font-semibold text-slate-700">
                    <tr>
                        <th class="py-3 px-4">Nomor & Tanggal Surat</th>
                        <th class="py-3 px-4">Perihal & Kegiatan</th>
                        <th class="py-3 px-4">Penerima / Mahasiswa</th>
                        <th class="py-3 px-4">Pengusul</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($letters as $letter)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-3 px-4">
                                <div class="font-mono font-bold text-slate-900 select-all">{{ $letter->letter_number }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $letter->letter_date->translatedFormat('d F Y') }}</div>
                                <span class="inline-block mt-1 bg-slate-100 text-slate-700 text-[10px] px-1.5 py-0.2 rounded font-medium">
                                    {{ $letter->category_label }}
                                </span>
                            </td>

                            <td class="py-3 px-4 max-w-xs">
                                <div class="font-semibold text-slate-900 truncate" title="{{ $letter->title }}">{{ $letter->title }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 truncate" title="{{ $letter->event_name }}">{{ $letter->event_name }}</div>
                            </td>

                            <td class="py-3 px-4">
                                @if($letter->recipients->count() > 0)
                                    <div class="font-medium text-slate-800">{{ $letter->recipients->first()->student_name }}</div>
                                    <div class="text-[11px] font-mono text-slate-500">{{ $letter->recipients->first()->nim }}</div>
                                    @if($letter->recipients->count() > 1)
                                        <div class="text-[10px] text-blue-800 font-semibold mt-0.5">+{{ $letter->recipients->count() - 1 }} mahasiswa lain</div>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Tidak terlampir</span>
                                @endif
                            </td>

                            <td class="py-3 px-4">
                                <div class="text-slate-800 font-medium">{{ $letter->submitter?->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $letter->submitter?->jabatan }}</div>
                            </td>

                            <td class="py-3 px-4 text-center">
                                @if($letter->isApproved())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold badge-emerald">
                                        Sah / Disahkan
                                    </span>
                                @elseif($letter->isPending())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold badge-amber">
                                        Menunggu
                                    </span>
                                @elseif($letter->isRevoked())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold badge-crimson">
                                        Dicabut
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold badge-crimson">
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('letters.show', $letter) }}" class="inline-block px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition" title="Lihat Detail">
                                    Detail
                                </a>

                                @if($letter->isApproved())
                                    <a href="{{ route('letters.download', $letter) }}" class="inline-block px-2.5 py-1 rounded bg-[#0F172A] hover:bg-slate-800 text-white font-semibold transition" title="Unduh PDF Resmi">
                                        Unduh
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Tidak ada surat yang sesuai dengan kriteria filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (md:hidden) -->
        <div class="md:hidden divide-y divide-slate-100">
            @forelse($letters as $letter)
                <div class="p-4 bg-white hover:bg-slate-50/70 transition space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-mono font-bold text-sm text-[#0F172A] select-all">{{ $letter->letter_number }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $letter->letter_date->translatedFormat('d F Y') }}</div>
                        </div>
                        <div>
                            @if($letter->isApproved())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold badge-emerald">
                                    Sah / Disahkan
                                </span>
                            @elseif($letter->isPending())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold badge-amber">
                                    Menunggu
                                </span>
                            @elseif($letter->isRevoked())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold badge-crimson">
                                    Dicabut
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold badge-crimson">
                                    Ditolak
                                </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="font-bold text-xs text-slate-900 leading-snug">{{ $letter->title }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $letter->event_name }}</div>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="bg-slate-100 text-slate-700 text-[10px] px-2 py-0.5 rounded font-medium">
                            {{ $letter->category_label }}
                        </span>
                        @if($letter->recipients->count() > 0)
                            <span class="bg-blue-50 text-blue-800 text-[10px] px-2 py-0.5 rounded font-medium border border-blue-200">
                                {{ $letter->recipients->count() }} Mahasiswa
                            </span>
                        @endif
                        <span class="text-[10px] text-slate-400 font-mono ml-auto">
                            Oleh: {{ $letter->submitter?->name }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 pt-1 border-t border-slate-100">
                        <a
                            href="{{ route('letters.show', $letter) }}"
                            class="flex-1 py-2 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold text-center transition min-h-[40px] flex items-center justify-center"
                        >
                            Detail Surat
                        </a>

                        @if($letter->isApproved())
                            <a
                                href="{{ route('letters.download', $letter) }}"
                                class="flex-1 py-2 px-3 rounded-lg bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-semibold text-center transition min-h-[40px] flex items-center justify-center space-x-1"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                                <span>Unduh PDF</span>
                            </a>
                        @elseif($letter->isPending() && auth()->user()->canSign())
                            <a
                                href="{{ route('approvals.show', $letter) }}"
                                class="flex-1 py-2 px-3 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold text-center transition min-h-[40px] flex items-center justify-center space-x-1"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Sahkan</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 px-4 text-center text-slate-400">
                    Tidak ada surat yang sesuai dengan kriteria filter pencarian.
                </div>
            @endforelse
        </div>

        @if($letters->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                {{ $letters->links() }}
            </div>
        @endif
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboardHub() {
    return {
        activeTab: '{{ request('tab', 'query') }}',
        isDragging: false,
        isLoading: false,
        fileName: '',
        checkResult: null,

        handleFileDrop(event) {
            this.isDragging = false;
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.uploadAndCheck(files[0]);
            }
        },

        handleFileSelect(event) {
            const files = event.target.files;
            if (files.length > 0) {
                this.uploadAndCheck(files[0]);
            }
        },

        async uploadAndCheck(file) {
            if (file.type !== 'application/pdf') {
                alert('File harus berupa dokumen PDF (.pdf).');
                return;
            }

            this.fileName = file.name;
            this.isLoading = true;
            this.checkResult = null;

            const formData = new FormData();
            formData.append('pdf_file', file);

            try {
                const response = await window.axios.post('{{ route('verify.integrity') }}', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json'
                    }
                });
                this.checkResult = response.data;
            } catch (error) {
                if (error.response && error.response.data) {
                    this.checkResult = error.response.data;
                } else {
                    this.checkResult = {
                        match: false,
                        status_title: 'TERJADI KESALAHAN',
                        message: 'Gagal mengunggah file untuk diverifikasi. Pastikan ukuran file di bawah 10MB.',
                        hash: '-'
                    };
                }
            } finally {
                this.isLoading = false;
            }
        }
    };
}
</script>
@endpush
