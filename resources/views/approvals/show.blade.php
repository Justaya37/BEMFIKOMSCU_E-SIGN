@extends('layouts.app')

@section('title', 'Tinjau & Eksekusi Pengesahan: ' . $letter->letter_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" x-data="approvalReview()">

    <!-- Top Navigation Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-4 border-b border-slate-200">
        <div>
            <a href="{{ route('approvals.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium inline-flex items-center space-x-1">
                <span>&larr;</span>
                <span>Kembali ke Antrean Pengesahan</span>
            </a>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <h1 class="text-lg sm:text-xl font-bold text-[#0F172A] font-mono">{{ $letter->letter_number }}</h1>
                @if($letter->isPending())
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold badge-amber">Menunggu Persetujuan</span>
                @elseif($letter->isApproved())
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold badge-emerald">Sudah Disahkan</span>
                @elseif($letter->isRevoked())
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold badge-crimson">Pengesahan Dicabut</span>
                @else
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold badge-crimson">Ditolak</span>
                @endif
            </div>
        </div>

        <div class="text-left sm:text-right bg-slate-50 sm:bg-transparent p-2.5 sm:p-0 rounded-lg border sm:border-0 border-slate-200">
            <span class="text-[11px] text-slate-500 block">Pejabat Otorisasi:</span>
            <span class="text-xs font-bold text-slate-900">{{ auth()->user()->name }}</span>
            <span class="text-[11px] text-blue-900 font-semibold block">{{ auth()->user()->jabatan }}</span>
        </div>
    </div>

    <!-- MOBILE WORKSPACE TABS (lg:hidden) - Memudahkan Ketua / Wakil di Layar Handphone -->
    <div class="lg:hidden flex items-center bg-slate-200/90 p-1 rounded-xl mb-4 text-xs font-semibold">
        <button
            type="button"
            @click="mobileWorkspaceTab = 'document'"
            :class="mobileWorkspaceTab === 'document' ? 'bg-[#0F172A] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
            class="flex-1 py-2.5 rounded-lg flex items-center justify-center space-x-1.5 transition cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>1. Pratinjau Dokumen</span>
        </button>
        <button
            type="button"
            @click="mobileWorkspaceTab = 'decision'"
            :class="mobileWorkspaceTab === 'decision' ? 'bg-[#1E3A8A] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
            class="flex-1 py-2.5 rounded-lg flex items-center justify-center space-x-1.5 transition cursor-pointer"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            <span>2. Keputusan & PIN</span>
        </button>
    </div>

    <!-- SPLIT-SCREEN WORKSPACE (DESKTOP) OR STACKED (MOBILE) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- SISI KIRI: PRATINJAU DOKUMEN DRAF PDF ASLI DENGAN ATUR STEMPEL LANGSUNG (7 Cols) -->
        <div
            class="lg:col-span-7 card-clean rounded-lg overflow-hidden flex flex-col h-[520px] sm:h-[650px] lg:h-[820px]"
            :class="{ 'hidden lg:flex': mobileWorkspaceTab !== 'document', 'flex': mobileWorkspaceTab === 'document' }"
        >
            @if($letter->isPending())
                <!-- Header Toolbar Editor (Responsive Stack) -->
                <div class="px-3 sm:px-4 py-2 sm:py-2.5 bg-slate-900 text-white flex flex-wrap sm:flex-nowrap items-center justify-between gap-2 text-xs shrink-0">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-bold text-[11px] sm:text-xs">Atur Posisi Stempel</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-[11px]">
                        <!-- View Mode Switch -->
                        <div class="bg-slate-800 p-0.5 rounded border border-slate-700 flex items-center space-x-0.5">
                            <button
                                type="button"
                                @click="viewMode = 'interactive'"
                                :class="viewMode === 'interactive' ? 'bg-blue-800 text-white font-bold shadow-xs' : 'text-slate-400 hover:text-white'"
                                class="px-2 py-0.5 rounded text-[10px] transition"
                            >
                                ✏️ Mode Geser
                            </button>
                            <button
                                type="button"
                                @click="viewMode = 'iframe'"
                                :class="viewMode === 'iframe' ? 'bg-blue-800 text-white font-bold shadow-xs' : 'text-slate-400 hover:text-white'"
                                class="px-2 py-0.5 rounded text-[10px] transition"
                            >
                                📄 Asli
                            </button>
                        </div>

                        <!-- Page Selector -->
                        <div class="flex items-center space-x-1 shrink-0 whitespace-nowrap" x-show="viewMode === 'interactive'">
                            <button type="button" @click="changePage(currentPage - 1)" :disabled="currentPage <= 1" title="Halaman Sebelumnya" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer font-bold text-white whitespace-nowrap text-[10px]">&lt; Prev</button>
                            <span class="font-mono text-slate-300 px-1 whitespace-nowrap text-[10px]">Hal <span x-text="currentPage"></span>/<span x-text="totalPages"></span></span>
                            <button type="button" @click="changePage(currentPage + 1)" :disabled="currentPage >= totalPages" title="Halaman Berikutnya" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer font-bold text-white whitespace-nowrap text-[10px]">Next &gt;</button>
                        </div>

                        <!-- Live Coordinates indicator -->
                        <div class="bg-slate-800 px-2 py-0.5 rounded font-mono text-emerald-400 text-[10px] sm:text-[11px] font-bold border border-slate-700">
                            X=<span x-text="cap_x.toFixed(1)"></span>, Y=<span x-text="cap_y.toFixed(1)"></span>
                        </div>
                    </div>
                </div>

                <!-- Sub-bar Petunjuk & Pindah Halaman Target Cap -->
                <div x-show="viewMode === 'interactive'" class="bg-blue-50 border-b border-blue-200 px-3.5 py-1.5 text-[11px] text-blue-900 flex items-center justify-between shrink-0">
                    <div class="flex items-center space-x-2">
                        <template x-if="currentPage === stamp_page">
                            <span class="flex items-center space-x-1.5 font-semibold text-emerald-900">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span><b>Halaman Target Cap & TTD (Hal. <span x-text="stamp_page"></span>):</b> Klik atau geser stempel tepat ke atas draf tanda tangan!</span>
                            </span>
                        </template>
                        <template x-if="currentPage !== stamp_page">
                            <span class="flex items-center space-x-2 text-slate-700">
                                <span class="text-slate-500">Target stempel saat ini di <b>Hal. <span x-text="stamp_page"></span></b>.</span>
                                <button
                                    type="button"
                                    @click="setStampPage(currentPage)"
                                    class="px-2.5 py-0.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-[11px] font-bold shadow-xs transition cursor-pointer flex items-center space-x-1"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span>Pindahkan Cap ke Halaman Ini (Hal. <span x-text="currentPage"></span>)</span>
                                </button>
                            </span>
                        </template>
                    </div>
                    <span class="text-[10px] text-slate-500 font-mono hidden sm:inline" x-text="`Hal. ${currentPage} dari ${totalPages} (${pageWidthMm.toFixed(0)}x${pageHeightMm.toFixed(0)} mm)`"></span>
                </div>

                <!-- Scrollable PDF Viewport Area (Interactive Canvas) -->
                <div x-show="viewMode === 'interactive'" id="pdf-container" class="flex-grow bg-slate-800 overflow-auto p-4 flex justify-center items-start relative select-none">
                    <!-- Loading State -->
                    <div x-show="pdfLoading" class="absolute inset-0 bg-slate-900/80 backdrop-blur-xs flex flex-col items-center justify-center text-white z-30">
                        <svg class="animate-spin h-8 w-8 text-blue-400 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span class="text-xs font-semibold">Memuat berkas draf PDF...</span>
                    </div>

                    <!-- Error Fallback State -->
                    <div x-show="pdfError" class="absolute inset-0 bg-slate-900/90 flex flex-col items-center justify-center text-white z-20 p-6 text-center" style="display: none;">
                        <p class="text-xs text-rose-300 mb-1" x-text="pdfErrorMessage || 'Tampilan interaktif memerlukan pembaca PDF bawaan browser.'"></p>
                        <p class="text-[11px] text-slate-400 mb-3">Terjadi kendala saat merender kanvas interaktif.</p>
                        <button type="button" @click="viewMode = 'iframe'" class="px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded text-xs font-semibold">
                            Beralih ke Pembaca PDF Asli (Iframe)
                        </button>
                    </div>

                    <!-- PDF Canvas & Interactive Overlay Container -->
                    <div
                        id="canvas-wrapper"
                        class="bg-white shadow-2xl rounded-xs self-start my-auto border border-slate-300 relative inline-block select-none"
                        :style="`width: ${canvasWidth}px; height: ${canvasHeight}px; position: relative; display: inline-block;`"
                        @click="onDocumentClick($event)"
                    >
                        <!-- Actual PDF Canvas rendered by PDF.js -->
                        <canvas id="pdf-render-canvas" class="block bg-white z-0 pointer-events-none" :style="`width: ${canvasWidth}px; height: ${canvasHeight}px; display: block;`"></canvas>

                        <!-- Interactive Stamp Element (Hanya tampil jika sedang melihat stamp_page) -->
                        <div
                            id="draggable-stamp"
                            x-show="currentPage === stamp_page"
                            @mousedown="startDrag($event)"
                            @touchstart="startTouchDrag($event)"
                            class="absolute cursor-grab active:cursor-grabbing border-2 border-dashed border-blue-600 bg-blue-500/10 hover:bg-blue-500/20 rounded z-20 select-none group"
                            :style="`left: ${stampLeftPx}px; top: ${stampTopPx}px; width: ${stampWidthPx}px; height: ${stampHeightPx}px;`"
                            title="Tahan dan geser (drag) ke posisi tanda tangan pada dokumen!"
                        >
                            <!-- Cap BEM Transparent PNG Image -->
                            <img
                                src="{{ asset('images/cap_bem.png') }}"
                                alt="Cap BEM FIKOM"
                                class="w-full h-full object-contain pointer-events-none opacity-90 drop-shadow-xs"
                            >

                            <!-- Optional Signer Signature Image Underneath Cap -->
                            <div x-show="include_signature_img" class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-90">
                                <img
                                    src="{{ asset('storage/' . (auth()->user()->signature_image_path ?: 'signatures/ttd_ketua.png')) }}"
                                    alt="TTD Digital"
                                    class="w-3/4 h-1/2 object-contain"
                                >
                            </div>

                            <!-- Badge Label on Stamp -->
                            <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-[#0F172A] text-white text-[9px] font-bold px-2 py-0.5 rounded shadow pointer-events-none whitespace-nowrap flex items-center space-x-1 border border-slate-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>Cap BEM (Hal. <span x-text="stamp_page"></span>)</span>
                            </div>

                            <!-- Coordinate Badge on Stamp -->
                            <div class="absolute -bottom-5 right-0 bg-blue-900 text-white text-[8px] font-mono px-1 rounded shadow pointer-events-none whitespace-nowrap">
                                <span x-text="cap_x.toFixed(1)"></span>, <span x-text="cap_y.toFixed(1)"></span> mm
                            </div>
                        </div>

                        <!-- QR Code Footer Badge Preview on Document (Hanya tampil di stamp_page) -->
                        <div
                            x-show="currentPage === stamp_page"
                            class="absolute pointer-events-none bg-white/95 border border-slate-300 rounded-xs p-1 flex items-center space-x-1.5 shadow-xs z-10"
                            :style="qrBadgeStyle"
                        >
                            <div class="w-4 h-4 bg-slate-900 flex items-center justify-center text-white shrink-0">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h8v8H3zm2 2v4h4V5zm8-2h8v8h-8zm2 2v4h4V5zM3 13h8v8H3zm2 2v4h4v-4zm13-2h3v2h-3zm-5 0h2v3h-2zm2 3h3v2h-3zm-2 2h2v3h-2zm5-2h3v5h-3zm-2 3h2v2h-2z"/>
                                </svg>
                            </div>
                            <div class="leading-none text-[7px] text-slate-800">
                                <b class="block text-slate-900 font-bold">BEM FIKOM E-SIGN</b>
                                <span class="text-[6px] text-slate-500 font-mono">Verifikasi Sah</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Iframe View Mode Fallback -->
                <div x-show="viewMode === 'iframe'" class="flex-grow bg-slate-200">
                    <iframe
                        src="/letters/{{ $letter->id }}/file/draft"
                        class="w-full h-full border-0"
                        title="Pratinjau Draf PDF Standar"
                    ></iframe>
                </div>

                <!-- Mobile Switch Bar in Document View (lg:hidden) -->
                <div class="lg:hidden p-2.5 bg-slate-900 border-t border-slate-700 flex items-center justify-between shrink-0">
                    <div class="text-[11px] text-slate-300">
                        Cap: <span class="font-mono text-emerald-400 font-bold" x-text="`Hal. ${stamp_page} (X:${cap_x.toFixed(1)}, Y:${cap_y.toFixed(1)})`"></span>
                    </div>
                    <button
                        type="button"
                        @click="mobileWorkspaceTab = 'decision'"
                        class="px-3 py-1.5 rounded-lg bg-[#1E3A8A] hover:bg-blue-700 text-white text-xs font-bold flex items-center space-x-1.5 transition shadow-xs cursor-pointer"
                    >
                        <span>Lanjut Sahkan (PIN) &rarr;</span>
                    </button>
                </div>
            @else
                <!-- Dokumen yang sudah disahkan / ditolak (Tampilan Iframe Reader Standar) -->
                <div class="px-4 py-2.5 bg-slate-100 border-b border-slate-200 flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-800 flex items-center space-x-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Dokumen Resmi Disahkan</span>
                    </span>
                    <span class="text-[11px] font-mono text-emerald-700 font-bold">Tervalidasi Digital</span>
                </div>
                <div class="flex-grow bg-slate-200">
                    <iframe
                        src="{{ route('letters.stream', ['letter' => $letter, 'type' => 'signed']) }}"
                        class="w-full h-full border-0"
                        title="Pratinjau PDF Resmi"
                    ></iframe>
                </div>
            @endif
        </div>

        <!-- SISI KANAN: DETAIL METADATA + PENERIMA + PANEL AKSI PENGESAHAN (5 Cols) -->
        <div
            class="lg:col-span-5 space-y-5"
            :class="{ 'hidden lg:block': mobileWorkspaceTab !== 'decision', 'block': mobileWorkspaceTab === 'decision' }"
        >

            <!-- ACTION PANEL (Prominent) -->
            <div class="card-clean rounded-lg p-5 border-l-4 {{ $letter->isPending() ? 'border-l-amber-500 bg-amber-50/20' : ($letter->isApproved() ? 'border-l-emerald-600 bg-emerald-50/20' : 'border-l-rose-500') }}">
                <h3 class="text-sm font-bold text-[#0F172A] mb-1">Panel Keputusan Pengesahan</h3>
                <p class="text-xs text-slate-500 mb-4">
                    @if($letter->isPending())
                        Tinjau seluruh isi draf dokumen di sebelah kiri dengan cermat sebelum membubuhkan stempel dan tanda tangan digital.
                    @elseif($letter->isApproved())
                        Surat ini telah sah. Jika di kemudian hari ditemukan pelanggaran atau pemalsuan kegiatan, Anda dapat mencabut pengesahannya.
                    @else
                        Surat ini telah ditolak/dicabut dan tidak dapat disahkan ulang.
                    @endif
                </p>

                @if($letter->isPending())
                    <!-- Panel Pengaturan Cepat Cap & QR -->
                    <div class="mb-4 p-3 bg-white rounded border border-slate-200 text-xs space-y-2.5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-1.5">
                            <span class="font-bold text-slate-800 text-[11px]">Pengaturan Tata Letak Pengesahan</span>
                            <span class="text-[10px] text-emerald-800 bg-emerald-50 border border-emerald-200 px-1.5 py-0.2 rounded font-mono font-semibold">Live di Dokumen</span>
                        </div>

                        <!-- Info Posisi Cap Saat Ini -->
                        <div class="p-2 bg-blue-50/70 rounded border border-blue-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-500 block font-semibold">Posisi Cap di Dokumen:</span>
                                <span class="font-mono text-blue-900 font-bold text-xs">Hal. <span x-text="stamp_page"></span> &bull; X: <span x-text="cap_x.toFixed(1)"></span>, Y: <span x-text="cap_y.toFixed(1)"></span> mm</span>
                            </div>
                            <template x-if="currentPage !== stamp_page">
                                <button type="button" @click="changePage(stamp_page)" class="text-[9px] text-blue-700 bg-white px-1.5 py-0.5 rounded border border-blue-200 hover:bg-blue-50 cursor-pointer font-medium">
                                    Buka Hal. <span x-text="stamp_page"></span> &rarr;
                                </button>
                            </template>
                            <template x-if="currentPage === stamp_page">
                                <span class="text-[9px] text-emerald-800 bg-emerald-100/80 px-1.5 py-0.5 rounded font-semibold">
                                    Aktif di kanvas
                                </span>
                            </template>
                        </div>

                        <!-- Preset Snap -->
                        <div>
                            <span class="text-[10px] text-slate-500 font-semibold block mb-1">Pilihan Cepat (Snap):</span>
                            <div class="grid grid-cols-2 gap-1.5">
                                <button type="button" @click="applyPreset('ttd_kiri')" class="px-2 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded text-[11px] font-semibold text-slate-800 text-center">
                                    TTD Kiri (Ketua)
                                </button>
                                <button type="button" @click="applyPreset('ttd_kanan')" class="px-2 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded text-[11px] font-semibold text-slate-800 text-center">
                                    TTD Kanan
                                </button>
                            </div>
                        </div>

                        <!-- Letak QR Footer -->
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-700 mb-0.5">Posisi QR Code Footer:</label>
                            <select name="qr_position" x-model="qr_position" class="w-full px-2 py-1.5 bg-white border border-slate-300 rounded text-xs focus:ring-1 focus:ring-blue-800 focus:outline-none">
                                <option value="bottom_left">Ujung Kiri Bawah (Standar)</option>
                                <option value="bottom_right">Ujung Kanan Bawah</option>
                                <option value="bottom_center">Tengah Bawah</option>
                            </select>
                        </div>

                        <!-- Lebar Cap -->
                        <div>
                            <div class="flex justify-between items-center mb-0.5">
                                <label class="text-[10px] font-semibold text-slate-700">Ukuran Lebar Cap (mm):</label>
                                <span class="font-mono text-[10px] text-blue-900 font-bold" x-text="cap_width + ' mm'"></span>
                            </div>
                            <input
                                type="range"
                                min="25"
                                max="60"
                                step="1"
                                x-model.number="cap_width"
                                @input="onCapWidthChange()"
                                class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#1E3A8A]"
                            >
                        </div>

                        <!-- Checkbox TTD Digital -->
                        <div class="pt-1 border-t border-slate-100">
                            <label class="flex items-start space-x-1.5 cursor-pointer">
                                <input type="checkbox" x-model="include_signature_img" class="mt-0.5 rounded border-slate-300 text-blue-900">
                                <span class="text-[11px] text-slate-700 leading-tight">
                                    Sertakan coretan TTD digital di bawah cap
                                    <span class="text-[10px] text-slate-400 block">(Uncheck jika draf PDF sudah ada tanda tangan asli).</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Tombol Sahkan (Membuka Modal PIN) -->
                        <button
                            type="button"
                            @click="openSignModal = true"
                            class="flex-1 py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs rounded shadow-sm transition flex items-center justify-center space-x-1.5 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Sahkan Surat (PIN)</span>
                        </button>

                        <!-- Tombol Tolak (Membuka Modal Alasan) -->
                        <button
                            type="button"
                            @click="openRejectModal = true"
                            class="py-2.5 px-4 bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 font-semibold text-xs rounded transition flex items-center justify-center space-x-1.5 cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>Tolak Surat</span>
                        </button>
                    </div>
                @elseif($letter->isApproved())
                    <div class="space-y-3">
                        <div class="p-3 bg-white rounded border border-emerald-200 text-xs">
                            <span class="text-slate-500 block">Disahkan pada:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $letter->approved_at->format('d/m/Y H:i:s') }} WIB</span>
                        </div>

                        <!-- Tombol Revoke -->
                        <button
                            type="button"
                            @click="openRevokeModal = true"
                            class="w-full py-2 px-3 bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-300 rounded text-xs font-semibold transition cursor-pointer flex items-center justify-center space-x-1.5"
                        >
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>Cabut Pengesahan (Revoke)</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Detail Metadata Surat -->
            <div class="card-clean rounded-lg p-5 text-xs space-y-2.5">
                <h3 class="font-bold text-[#0F172A] border-b border-slate-100 pb-2">Detail Metadata</h3>

                <div>
                    <span class="text-slate-500 block">Perihal:</span>
                    <span class="font-semibold text-slate-900 block mt-0.5">{{ $letter->title }}</span>
                </div>

                <div>
                    <span class="text-slate-500 block">Kegiatan / Alasan:</span>
                    <span class="text-slate-800 block mt-0.5">{{ $letter->event_name }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-slate-500 block">Tanggal Surat:</span>
                        <span class="font-semibold text-slate-800">{{ $letter->letter_date->translatedFormat('d F Y') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Posisi QR Footer:</span>
                        <span class="font-mono text-slate-700 capitalize">{{ str_replace('_', ' ', $letter->qr_position ?: 'bottom_left') }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-500 block">Tata Letak Cap Stempel:</span>
                    <span class="font-mono text-blue-900 font-semibold">X: {{ $letter->cap_x ?? 28 }} mm, Y: {{ $letter->cap_y ?? 195 }} mm (Lebar: {{ $letter->cap_width ?? 38 }} mm)</span>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-500 block">Pengusul:</span>
                    <span class="text-slate-800 font-medium">{{ $letter->submitter?->name }} ({{ $letter->submitter?->jabatan }})</span>
                </div>
            </div>

            <!-- Daftar Mahasiswa Penerima -->
            <div class="card-clean rounded-lg p-5 text-xs">
                <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-100">
                    <h3 class="font-bold text-[#0F172A]">Daftar Mahasiswa ({{ $letter->recipients->count() }})</h3>
                    <span class="text-[10px] text-slate-400 font-mono">Cek Kebenaran NIM</span>
                </div>

                <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                    @forelse($letter->recipients as $idx => $r)
                        <div class="p-2 bg-slate-50 rounded border border-slate-200 flex items-center justify-between text-xs">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $r->student_name }}</div>
                                <div class="font-mono text-[11px] text-slate-600">NIM: {{ $r->nim }}</div>
                            </div>
                            @if($r->notes)
                                <span class="text-[10px] bg-slate-200 text-slate-700 px-1.5 py-0.2 rounded font-medium">{{ $r->notes }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="py-3 text-center text-slate-400">Tidak ada daftar mahasiswa terlampir.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL 1: KONFIRMASI PIN PENGESAHAN (ACTION 1) -->
    <div x-show="openSignModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="card-clean rounded-lg max-w-md w-full p-6 shadow-xl" @click.away="openSignModal = false">
            <div class="flex items-start justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-[#0F172A]">Otorisasi PIN Pengesahan Digital</h3>
                </div>
                <button type="button" @click="openSignModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <p class="text-xs text-slate-600 mb-4 leading-relaxed">
                Anda akan menandatangani secara digital surat nomor <b class="font-mono text-slate-900">{{ $letter->letter_number }}</b>. Sistem akan menempelkan stempel resmi BEM FIKOM, QR Code verifikasi, dan sertifikat kriptografi X.509 atas nama Anda.
            </p>

            <!-- Konfirmasi Posisi yang sudah diatur langsung di dokumen -->
            <div class="mb-4 p-2.5 bg-slate-50 rounded border border-slate-200 text-xs flex items-center justify-between">
                <div>
                    <span class="text-slate-500 text-[10px] block font-semibold uppercase">Posisi Cap di Dokumen:</span>
                    <span class="font-mono font-bold text-blue-900">Hal. <span x-text="stamp_page"></span> &bull; X: <span x-text="cap_x.toFixed(1)"></span> mm, Y: <span x-text="cap_y.toFixed(1)"></span> mm</span>
                </div>
                <div class="text-right">
                    <span class="text-slate-500 text-[10px] block font-semibold uppercase">Halaman & Posisi QR:</span>
                    <span class="font-mono text-slate-800 font-semibold" x-text="`Hal. ${stamp_page} (${qr_position.replace('_', ' ')})`"></span>
                </div>
            </div>

            <form action="{{ route('approvals.sign', $letter) }}" method="POST">
                @csrf
                <input type="hidden" name="cap_x" :value="cap_x">
                <input type="hidden" name="cap_y" :value="cap_y">
                <input type="hidden" name="cap_width" :value="cap_width">
                <input type="hidden" name="qr_position" :value="qr_position">
                <input type="hidden" name="stamp_page" :value="stamp_page">
                <input type="hidden" name="include_signature_img" :value="include_signature_img ? '1' : '0'">

                <div class="mb-4">
                    <label for="signing_pin" class="block text-xs font-bold text-slate-700 mb-1">
                        Masukkan 6 Digit PIN Pengesahan Anda
                    </label>
                    <input
                        type="password"
                        id="signing_pin"
                        name="signing_pin"
                        required
                        maxlength="6"
                        pattern="\d{6}"
                        inputmode="numeric"
                        autofocus
                        placeholder="••••••"
                        class="w-full px-4 py-3 bg-white border border-slate-300 rounded text-center font-mono font-extrabold text-xl tracking-[0.5em] focus:ring-2 focus:ring-emerald-600 focus:outline-none"
                    >
                    <div class="text-[11px] text-slate-400 mt-1">
                        * PIN demo bawaan Ketua: <code class="font-mono font-bold">123456</code> | Wakil: <code class="font-mono font-bold">654321</code>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button
                        type="button"
                        @click="openSignModal = false"
                        class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs font-semibold shadow-sm transition"
                    >
                        Eksekusi Pengesahan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: TOLAK SURAT (ACTION 2) -->
    <div x-show="openRejectModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="card-clean rounded-lg max-w-md w-full p-6 shadow-xl" @click.away="openRejectModal = false">
            <div class="flex items-start justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-bold text-rose-900">Tolak Pengesahan Surat Resmi</h3>
                <button type="button" @click="openRejectModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form action="{{ route('approvals.reject', $letter) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reject_reason" class="block text-xs font-semibold text-slate-700 mb-1">
                        Alasan Penolakan Resmi <span class="text-rose-600">*</span>
                    </label>
                    <textarea
                        id="reject_reason"
                        name="reason"
                        rows="3"
                        required
                        placeholder="Jelaskan alasan penolakan secara jelas (misal: nomor surat keliru, nama kegiatan tidak terdaftar, atau lampiran mahasiswa tidak lengkap)..."
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="openRejectModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded text-xs font-semibold shadow-sm transition">
                        Konfirmasi Penolakan Surat
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: PENCABUTAN PENGESAHAN (REVOKE) -->
    <div x-show="openRevokeModal" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="card-clean rounded-lg max-w-md w-full p-6 shadow-xl" @click.away="openRevokeModal = false">
            <div class="flex items-start justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-bold text-rose-900">Cabut Pengesahan Surat (Revoke)</h3>
                <button type="button" @click="openRevokeModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <p class="text-xs text-rose-800 mb-4">
                <b>PERINGATAN:</b> Pencabutan akan mengubah status surat seketika menjadi <b>DICABUT</b> pada scan QR code dan pencarian publik.
            </p>

            <form action="{{ route('approvals.revoke', $letter) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="revoke_reason" class="block text-xs font-semibold text-slate-700 mb-1">
                        Alasan Pencabutan Resmi <span class="text-rose-600">*</span>
                    </label>
                    <textarea
                        id="revoke_reason"
                        name="reason"
                        rows="3"
                        required
                        placeholder="Tuliskan dasar pencabutan surat resmi ini..."
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none"
                    ></textarea>
                </div>

                <div class="mb-4">
                    <label for="revoke_pin" class="block text-xs font-bold text-slate-700 mb-1">
                        PIN Otorisasi 6 Digit <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="password"
                        id="revoke_pin"
                        name="signing_pin"
                        required
                        maxlength="6"
                        pattern="\d{6}"
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-center font-mono font-bold text-lg tracking-widest focus:ring-2 focus:ring-rose-500 focus:outline-none"
                    >
                </div>

                <div class="flex items-center justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="openRevokeModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded text-xs font-semibold shadow-sm transition">
                        Cabut Pengesahan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Pastikan worker PDF.js mengarah ke path relatif lokal domain yang sama
if (window.pdfjsLib) {
    window.pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';
}

function approvalReview() {
    // Simpan instance raw PDF.js di luar reactive proxy Alpine agar private members tidak rusak
    let rawPdfDoc = null;
    let activeRenderTask = null;

    return {
        openSignModal: false,
        openRejectModal: false,
        openRevokeModal: false,
        viewMode: 'interactive',
        mobileWorkspaceTab: 'document',

        // Koordinat Cap BEM dalam mm (Presisi 1 desimal)
        cap_x: {{ (float) ($letter->cap_x ?? 28.0) }},
        cap_y: {{ (float) ($letter->cap_y ?? 195.0) }},
        cap_width: {{ (float) ($letter->cap_width ?? 38.0) }},
        qr_position: '{{ $letter->qr_position ?? 'bottom_left' }}',
        include_signature_img: {{ $letter->include_signature_img ? 'true' : 'false' }},

        // State PDF Renderer (Primitif)
        pdfLoading: true,
        pdfError: false,
        pdfErrorMessage: '',
        stamp_page: {{ max(1, (int) ($letter->stamp_page ?? 1)) }},
        currentPage: {{ max(1, (int) ($letter->stamp_page ?? 1)) }},
        totalPages: 1,
        pxPerMm: 1.0,
        pageWidthMm: 210.0,
        pageHeightMm: 297.0,
        canvasWidth: 680,
        canvasHeight: 962,
        resizeTimeout: null,

        // State Dragging
        isDragging: false,

        // Computed Pixels untuk Tata Letak CSS Real-Time
        get stampHeightMm() {
            return this.cap_width / (1024 / 576); // Cap aspect ratio 16:9 = 1.777778
        },
        get stampLeftPx() {
            return Math.round(this.cap_x * this.pxPerMm);
        },
        get stampTopPx() {
            return Math.round(this.cap_y * this.pxPerMm);
        },
        get stampWidthPx() {
            return Math.round(this.cap_width * this.pxPerMm);
        },
        get stampHeightPx() {
            return Math.round(this.stampHeightMm * this.pxPerMm);
        },
        get qrBadgeStyle() {
            const badgeW = Math.round(76 * this.pxPerMm);
            const badgeH = Math.round(17.5 * this.pxPerMm);
            const marginB = Math.round(8 * this.pxPerMm);
            const marginS = Math.round(12 * this.pxPerMm);

            if (this.qr_position === 'bottom_right') {
                return `right: ${marginS}px; bottom: ${marginB}px; width: ${badgeW}px; height: ${badgeH}px;`;
            } else if (this.qr_position === 'bottom_center') {
                return `left: 50%; transform: translateX(-50%); bottom: ${marginB}px; width: ${badgeW}px; height: ${badgeH}px;`;
            }
            return `left: ${marginS}px; bottom: ${marginB}px; width: ${badgeW}px; height: ${badgeH}px;`;
        },

        async init() {
            @if($letter->isPending())
                await this.initPdfViewer();
                window.addEventListener('resize', () => {
                    clearTimeout(this.resizeTimeout);
                    this.resizeTimeout = setTimeout(() => {
                        if (rawPdfDoc && this.viewMode === 'interactive') {
                            this.renderPage(this.currentPage);
                        }
                    }, 200);
                });
            @endif
        },

        async initPdfViewer() {
            try {
                this.pdfLoading = true;
                this.pdfError = false;
                this.pdfErrorMessage = '';

                // Tunggu library pdfjsLib siap jika masih di-load
                let attempts = 0;
                while (!window.pdfjsLib && attempts < 40) {
                    await new Promise(r => setTimeout(r, 50));
                    attempts++;
                }

                if (!window.pdfjsLib) {
                    throw new Error("Library PDF.js tidak ditemukan");
                }

                // Gunakan path relatif lokal pada origin yang sama
                pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';

                const url = '/letters/{{ $letter->id }}/file/draft';

                // Menggunakan fetch arrayBuffer agar data binary stabil dan bebas isu proxy
                const response = await fetch(url, { credentials: 'same-origin' });
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: Gagal mengunduh berkas draf`);
                }
                const arrayBuffer = await response.arrayBuffer();
                const uint8Array = new Uint8Array(arrayBuffer);

                const loadingTask = pdfjsLib.getDocument({
                    data: uint8Array
                });

                rawPdfDoc = await loadingTask.promise;
                this.totalPages = rawPdfDoc.numPages;

                // Langsung buka halaman sesuai stamp_page yang dipilih Sekretaris (bukan default paksa halaman terakhir)
                const savedPage = {{ max(1, (int) ($letter->stamp_page ?? 1)) }};
                this.stamp_page = (savedPage >= 1 && savedPage <= this.totalPages) ? savedPage : 1;
                this.currentPage = this.stamp_page;

                await this.$nextTick();
                await this.renderPage(this.currentPage);
            } catch (err) {
                console.error("Gagal memuat dokumen draf PDF via PDF.js:", err);
                this.pdfError = true;
                this.pdfErrorMessage = err?.message || 'Gagal memuat dokumen PDF';
            } finally {
                this.pdfLoading = false;
            }
        },

        async renderPage(pageNo) {
            if (!rawPdfDoc) return;
            this.pdfLoading = true;

            // Batalkan tugas render sebelumnya jika ada untuk menghindari konflik render task
            if (activeRenderTask) {
                try {
                    activeRenderTask.cancel();
                } catch (_) {}
                activeRenderTask = null;
            }

            try {
                await this.$nextTick();
                const page = await rawPdfDoc.getPage(pageNo);
                const container = document.getElementById('pdf-container');
                const containerW = container ? (container.clientWidth - 48) : 680;
                const displayWidth = Math.max(480, Math.min(containerW > 0 ? containerW : 680, 720));

                // 1 pt = 0.352778 mm (25.4 / 72.0)
                const unscaledViewport = page.getViewport({ scale: 1.0 });
                this.pageWidthMm = unscaledViewport.width * 0.352778;
                this.pageHeightMm = unscaledViewport.height * 0.352778;

                const displayHeight = Math.round(displayWidth * (this.pageHeightMm / this.pageWidthMm));
                this.canvasWidth = displayWidth;
                this.canvasHeight = displayHeight;
                this.pxPerMm = displayWidth / this.pageWidthMm;

                const canvas = document.getElementById('pdf-render-canvas');
                if (canvas) {
                    // Render dengan scale 2x DPR agar teks tajam dan tidak buram di belakang cap
                    const dpr = Math.min(window.devicePixelRatio || 1, 2);
                    const scale = (displayWidth / unscaledViewport.width) * dpr;
                    const viewport = page.getViewport({ scale: scale });

                    canvas.width = Math.floor(viewport.width);
                    canvas.height = Math.floor(viewport.height);
                    canvas.style.display = 'block';
                    canvas.style.width = displayWidth + 'px';
                    canvas.style.height = displayHeight + 'px';

                    const context = canvas.getContext('2d');
                    context.fillStyle = '#FFFFFF';
                    context.fillRect(0, 0, canvas.width, canvas.height);

                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport,
                    };

                    activeRenderTask = page.render(renderContext);
                    await activeRenderTask.promise;
                    activeRenderTask = null;
                    this.pdfError = false;
                }

                // Periksa batas stempel
                this.clampStampWithinBounds();
            } catch (e) {
                if (e && e.name === 'RenderingCancelledException') {
                    return; // Normal jika dibatalkan oleh perpindahan halaman/resize
                }
                console.error("Render page error:", e);
                this.pdfError = true;
                this.pdfErrorMessage = 'Gagal merender halaman: ' + (e?.message || 'Error');
            } finally {
                this.pdfLoading = false;
            }
        },

        async changePage(newPage) {
            if (newPage < 1 || newPage > this.totalPages) return;
            this.currentPage = newPage;
            await this.renderPage(newPage);
        },

        setStampPage(pageNo) {
            this.stamp_page = Math.max(1, Math.min(this.totalPages || 1, pageNo));
            if (this.currentPage !== this.stamp_page) {
                this.changePage(this.stamp_page);
            }
        },

        onCapWidthChange() {
            this.clampStampWithinBounds();
        },

        clampStampWithinBounds() {
            const maxX = Math.max(0, this.pageWidthMm - this.cap_width);
            const maxY = Math.max(0, this.pageHeightMm - this.stampHeightMm);
            if (this.cap_x > maxX) {
                this.cap_x = Number(maxX.toFixed(1));
            }
            if (this.cap_y > maxY) {
                this.cap_y = Number(maxY.toFixed(1));
            }
            if (this.cap_x < 0) this.cap_x = 0;
            if (this.cap_y < 0) this.cap_y = 0;
        },

        startDrag(e) {
            e.stopPropagation();
            e.preventDefault();
            this.isDragging = true;

            const canvasWrapper = document.getElementById('canvas-wrapper');
            const stampEl = document.getElementById('draggable-stamp');
            if (!canvasWrapper || !stampEl) return;

            const stampRect = stampEl.getBoundingClientRect();
            const offsetX = e.clientX - stampRect.left;
            const offsetY = e.clientY - stampRect.top;

            const onMouseMove = (moveEvent) => {
                if (!this.isDragging) return;
                const rect = canvasWrapper.getBoundingClientRect();

                const targetLeftPx = moveEvent.clientX - rect.left - offsetX;
                const targetTopPx = moveEvent.clientY - rect.top - offsetY;

                let newMmX = targetLeftPx / this.pxPerMm;
                let newMmY = targetTopPx / this.pxPerMm;

                const maxMmX = this.pageWidthMm - this.cap_width;
                const maxMmY = this.pageHeightMm - this.stampHeightMm;

                newMmX = Math.max(0, Math.min(maxMmX, newMmX));
                newMmY = Math.max(0, Math.min(maxMmY, newMmY));

                this.cap_x = Number(newMmX.toFixed(1));
                this.cap_y = Number(newMmY.toFixed(1));

                // Auto-scroll container jika mendekati batas atas/bawah
                const container = document.getElementById('pdf-container');
                if (container) {
                    const containerRect = container.getBoundingClientRect();
                    if (moveEvent.clientY > containerRect.bottom - 40) {
                        container.scrollTop += 12;
                    } else if (moveEvent.clientY < containerRect.top + 40) {
                        container.scrollTop -= 12;
                    }
                }
            };

            const onMouseUp = () => {
                this.isDragging = false;
                window.removeEventListener('mousemove', onMouseMove);
                window.removeEventListener('mouseup', onMouseUp);
            };

            window.addEventListener('mousemove', onMouseMove);
            window.addEventListener('mouseup', onMouseUp);
        },

        startTouchDrag(e) {
            e.stopPropagation();
            if (!e.touches || e.touches.length === 0) return;
            this.isDragging = true;

            const touch = e.touches[0];
            const canvasWrapper = document.getElementById('canvas-wrapper');
            const stampEl = document.getElementById('draggable-stamp');
            if (!canvasWrapper || !stampEl) return;

            const stampRect = stampEl.getBoundingClientRect();
            const offsetX = touch.clientX - stampRect.left;
            const offsetY = touch.clientY - stampRect.top;

            const onTouchMove = (moveEvent) => {
                if (!this.isDragging || !moveEvent.touches || moveEvent.touches.length === 0) return;
                moveEvent.preventDefault();
                const t = moveEvent.touches[0];
                const rect = canvasWrapper.getBoundingClientRect();

                const targetLeftPx = t.clientX - rect.left - offsetX;
                const targetTopPx = t.clientY - rect.top - offsetY;

                let newMmX = targetLeftPx / this.pxPerMm;
                let newMmY = targetTopPx / this.pxPerMm;

                const maxMmX = this.pageWidthMm - this.cap_width;
                const maxMmY = this.pageHeightMm - this.stampHeightMm;

                newMmX = Math.max(0, Math.min(maxMmX, newMmX));
                newMmY = Math.max(0, Math.min(maxMmY, newMmY));

                this.cap_x = Number(newMmX.toFixed(1));
                this.cap_y = Number(newMmY.toFixed(1));
            };

            const onTouchEnd = () => {
                this.isDragging = false;
                window.removeEventListener('touchmove', onTouchMove);
                window.removeEventListener('touchend', onTouchEnd);
            };

            window.addEventListener('touchmove', onTouchMove, { passive: false });
            window.addEventListener('touchend', onTouchEnd);
        },

        onDocumentClick(e) {
            if (this.isDragging) return;
            if (e.target.closest('#draggable-stamp')) return;

            // Jika pengguna klik di halaman yang bukan target stamp_page, jadikan halaman ini sebagai stamp_page
            if (this.currentPage !== this.stamp_page) {
                this.stamp_page = this.currentPage;
            }

            const canvasWrapper = document.getElementById('canvas-wrapper');
            if (!canvasWrapper) return;

            const rect = canvasWrapper.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;

            const halfW = this.cap_width / 2;
            const halfH = this.stampHeightMm / 2;

            let newMmX = (clickX / this.pxPerMm) - halfW;
            let newMmY = (clickY / this.pxPerMm) - halfH;

            const maxMmX = this.pageWidthMm - this.cap_width;
            const maxMmY = this.pageHeightMm - this.stampHeightMm;

            newMmX = Math.max(0, Math.min(maxMmX, newMmX));
            newMmY = Math.max(0, Math.min(maxMmY, newMmY));

            this.cap_x = Number(newMmX.toFixed(1));
            this.cap_y = Number(newMmY.toFixed(1));
        },

        applyPreset(preset) {
            const defaultY = Math.max(0, Math.min(this.pageHeightMm - this.stampHeightMm, this.pageHeightMm * 0.72));
            if (preset === 'ttd_kiri') {
                this.cap_x = Number(Math.max(0, Math.min(this.pageWidthMm - this.cap_width, 28.0)).toFixed(1));
                this.cap_y = Number(defaultY.toFixed(1));
            } else if (preset === 'ttd_kanan') {
                const rightX = this.pageWidthMm * 0.60;
                this.cap_x = Number(Math.max(0, Math.min(this.pageWidthMm - this.cap_width, rightX)).toFixed(1));
                this.cap_y = Number(defaultY.toFixed(1));
            }
        }
    };
}
</script>
@endpush
