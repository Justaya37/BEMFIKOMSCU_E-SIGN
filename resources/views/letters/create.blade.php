@extends('layouts.app')

@section('title', 'Ajukan Pengesahan Surat Resmi')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="letterCreateForm()">
    <!-- Breadcrumb & Header -->
    <div class="mb-6">
        <div class="text-xs font-mono font-bold text-blue-900 uppercase tracking-wider mb-1">Sekretariat BEM FIKOM</div>
        <h1 class="text-2xl font-bold text-[#0F172A]">Ajukan Pengesahan Surat Resmi Baru</h1>
        <p class="text-xs text-slate-500 mt-1">
            Unggah draf surat resmi dalam format PDF dan lengkapi rincian penerima untuk diajukan ke Ketua / Wakil Ketua BEM.
        </p>
    </div>

    <form action="{{ route('letters.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. METADATA SURAT -->
        <div class="card-clean rounded-lg p-6">
            <h2 class="text-sm font-bold text-[#0F172A] mb-4 pb-2 border-b border-slate-100 flex items-center space-x-2">
                <span class="w-5 h-5 rounded-full bg-[#0F172A] text-white text-xs flex items-center justify-center font-mono">1</span>
                <span>Metadata Resmi Surat</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="letter_number" class="block font-semibold text-slate-700">
                            Nomor Surat Resmi <span class="text-rose-600">*</span>
                        </label>
                        <a
                            href="https://docs.google.com/spreadsheets/d/1mtOwVlwR7iU6a1cnEkySZivieXwhvJPt/edit?gid=892539536#gid=892539536"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center space-x-1 text-[11px] font-semibold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 px-2 py-0.5 rounded transition group"
                            title="Buka Spreadsheet Buku Penomoran Surat Resmi BEM FIKOM"
                        >
                            <svg class="w-3.5 h-3.5 text-emerald-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Cek Buku Nomor Surat (Sheets) &rarr;</span>
                        </a>
                    </div>
                    <input
                        type="text"
                        id="letter_number"
                        name="letter_number"
                        value="{{ old('letter_number') }}"
                        required
                        placeholder="Contoh: 015/BEM-FIKOM/XI/2026"
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-sm font-mono focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                    >
                    <span class="text-[10px] text-slate-500 mt-1 block">
                        * Pastikan nomor surat sesuai urutan pada buku penomoran spreadsheet organisasi.
                    </span>
                </div>

                <div>
                    <label for="category" class="block font-semibold text-slate-700 mb-1">
                        Kategori Surat <span class="text-rose-600">*</span>
                    </label>
                    <select
                        id="category"
                        name="category"
                        required
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-sm focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                    >
                        <option value="surat_izin" {{ old('category') === 'surat_izin' ? 'selected' : '' }}>Surat Izin / Dispensasi Perkuliahan</option>
                        <option value="surat_tugas" {{ old('category') === 'surat_tugas' ? 'selected' : '' }}>Surat Tugas Delegasi / Panitia</option>
                        <option value="surat_undangan" {{ old('category') === 'surat_undangan' ? 'selected' : '' }}>Surat Undangan Resmi</option>
                        <option value="surat_keterangan" {{ old('category') === 'surat_keterangan' ? 'selected' : '' }}>Surat Keterangan Aktif Organisasi</option>
                        <option value="lainnya" {{ old('category') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="title" class="block font-semibold text-slate-700 mb-1">
                        Perihal / Judul Surat <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        placeholder="Contoh: Permohonan Dispensasi Mengikuti Final Lomba UI/UX Nasional"
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-sm focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                    >
                </div>

                <div>
                    <label for="event_name" class="block font-semibold text-slate-700 mb-1">
                        Nama Kegiatan / Alasan Surat <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="text"
                        id="event_name"
                        name="event_name"
                        value="{{ old('event_name') }}"
                        required
                        placeholder="Contoh: Hackathon Innovation Summit 2026"
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-sm focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                    >
                </div>

                <div>
                    <label for="letter_date" class="block font-semibold text-slate-700 mb-1">
                        Tanggal Surat Diterbitkan <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="date"
                        id="letter_date"
                        name="letter_date"
                        value="{{ old('letter_date', date('Y-m-d')) }}"
                        required
                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded text-sm font-mono focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                    >
                </div>
            </div>
        </div>

        <!-- 2. STAMPING CONFIGURATION & PDF DRAFT UPLOAD -->
        <div class="card-clean rounded-lg p-6">
            <h2 class="text-sm font-bold text-[#0F172A] mb-4 pb-2 border-b border-slate-100 flex items-center space-x-2">
                <span class="w-5 h-5 rounded-full bg-[#0F172A] text-white text-xs flex items-center justify-center font-mono">2</span>
                <span>Berkas Draf & Tata Letak Pengesahan (Pemisahan Cap & QR Code)</span>
            </h2>

            <!-- Grid: Kontrol Tata Letak & Visual Interactive Paper Preview -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
                <!-- Sisi Kiri (7 Kolom): Kontrol Posisi QR dan Koordinat Cap -->
                <div class="lg:col-span-7 space-y-4 text-xs">
                    <!-- A. Konfigurasi QR Code -->
                    <div class="p-3 bg-slate-50 rounded border border-slate-200">
                        <div class="font-bold text-[#0F172A] mb-2 flex items-center justify-between">
                            <span>1. Posisi QR Code Verifikasi (Ujung Bawah Surat)</span>
                            <span class="text-[10px] text-blue-900 bg-blue-100 px-1.5 py-0.5 rounded font-mono font-bold">Terpisah dari Cap</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="qr_position" class="block font-semibold text-slate-700 mb-1">
                                    Letak QR Code di Footer <span class="text-rose-600">*</span>
                                </label>
                                <select
                                    id="qr_position"
                                    name="qr_position"
                                    x-model="qr_position"
                                    class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded text-xs focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                                >
                                    <option value="bottom_left">Ujung Kiri Bawah (Standar Footer)</option>
                                    <option value="bottom_right">Ujung Kanan Bawah</option>
                                    <option value="bottom_center">Tengah Bawah</option>
                                </select>
                            </div>
                            <div>
                                <label for="stamp_page" class="block font-semibold text-slate-700 mb-1">
                                    Halaman Target Penempelan <span class="text-rose-600">*</span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <input
                                        type="number"
                                        id="stamp_page"
                                        name="stamp_page"
                                        min="1"
                                        :max="totalPages || 1"
                                        x-model.number="stamp_page"
                                        @change="onStampPageInputChange()"
                                        required
                                        class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded text-xs font-mono font-bold text-blue-900 focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                                    >
                                    <span class="text-xs font-mono text-slate-500 whitespace-nowrap" x-show="pdfLoaded">
                                        / <span x-text="totalPages"></span> Hal
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-500 mt-0.5 block">
                                    * Pilih halaman (1 s/d <span x-text="totalPages || 1"></span>) tempat tanda tangan berada
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- B. Konfigurasi Cap BEM FIKOM Manual -->
                    <div class="p-3 bg-slate-50 rounded border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-[#0F172A]">2. Tata Letak Cap BEM (Bisa Diubah Manual)</span>
                            <span class="text-[10px] text-emerald-800 bg-emerald-100 px-1.5 py-0.5 rounded font-mono font-bold">Transparan & Presisi</span>
                        </div>

                        <!-- Presets Cepat -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1 text-[11px]">Pilihan Posisi Cepat (Preset):</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-1.5">
                                <button
                                    type="button"
                                    @click="applyPreset('ttd_kiri')"
                                    :class="cap_preset === 'ttd_kiri' ? 'bg-[#0F172A] text-white font-bold' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                    class="px-2 py-1.5 rounded text-[11px] transition text-center"
                                >
                                    TTD Kiri
                                </button>
                                <button
                                    type="button"
                                    @click="applyPreset('ttd_kanan')"
                                    :class="cap_preset === 'ttd_kanan' ? 'bg-[#0F172A] text-white font-bold' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                    class="px-2 py-1.5 rounded text-[11px] transition text-center"
                                >
                                    TTD Kanan
                                </button>
                                <button
                                    type="button"
                                    @click="applyPreset('kanan_bawah')"
                                    :class="cap_preset === 'kanan_bawah' ? 'bg-[#0F172A] text-white font-bold' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                    class="px-2 py-1.5 rounded text-[11px] transition text-center"
                                >
                                    Kanan Bawah
                                </button>
                                <button
                                    type="button"
                                    @click="applyPreset('kiri_bawah')"
                                    :class="cap_preset === 'kiri_bawah' ? 'bg-[#0F172A] text-white font-bold' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                    class="px-2 py-1.5 rounded text-[11px] transition text-center"
                                >
                                    Kiri Bawah
                                </button>
                            </div>
                        </div>

                        <!-- Slider & Input Koordinat Manual -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-200">
                            <!-- Koordinat X -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label for="cap_x" class="font-semibold text-slate-700 text-[11px]">Posisi X (Horiz)</label>
                                    <span class="font-mono text-[10px] text-blue-900 font-bold" x-text="cap_x + ' mm'"></span>
                                </div>
                                <input
                                    type="range"
                                    min="5"
                                    max="165"
                                    step="1"
                                    x-model.number="cap_x"
                                    @input="cap_preset = 'custom'"
                                    class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#1E3A8A]"
                                >
                                <input
                                    type="number"
                                    id="cap_x"
                                    name="cap_x"
                                    x-model.number="cap_x"
                                    @input="cap_preset = 'custom'"
                                    class="w-full mt-1 px-2 py-1 bg-white border border-slate-300 rounded text-xs font-mono"
                                >
                            </div>

                            <!-- Koordinat Y -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label for="cap_y" class="font-semibold text-slate-700 text-[11px]">Posisi Y (Vertikal)</label>
                                    <span class="font-mono text-[10px] text-blue-900 font-bold" x-text="cap_y + ' mm'"></span>
                                </div>
                                <input
                                    type="range"
                                    min="30"
                                    max="265"
                                    step="1"
                                    x-model.number="cap_y"
                                    @input="cap_preset = 'custom'"
                                    class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#1E3A8A]"
                                >
                                <input
                                    type="number"
                                    id="cap_y"
                                    name="cap_y"
                                    x-model.number="cap_y"
                                    @input="cap_preset = 'custom'"
                                    class="w-full mt-1 px-2 py-1 bg-white border border-slate-300 rounded text-xs font-mono"
                                >
                            </div>

                            <!-- Ukuran Lebar Cap -->
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label for="cap_width" class="font-semibold text-slate-700 text-[11px]">Lebar Cap</label>
                                    <span class="font-mono text-[10px] text-blue-900 font-bold" x-text="cap_width + ' mm'"></span>
                                </div>
                                <input
                                    type="range"
                                    min="25"
                                    max="55"
                                    step="1"
                                    x-model.number="cap_width"
                                    class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#1E3A8A]"
                                >
                                <input
                                    type="number"
                                    id="cap_width"
                                    name="cap_width"
                                    x-model.number="cap_width"
                                    class="w-full mt-1 px-2 py-1 bg-white border border-slate-300 rounded text-xs font-mono"
                                >
                            </div>
                        </div>

                        <!-- Opsi TTD Digital -->
                        <div class="pt-2 border-t border-slate-200">
                            <label class="flex items-start space-x-2 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="include_signature_img"
                                    value="1"
                                    x-model="include_signature_img"
                                    class="mt-0.5 rounded border-slate-300 text-[#1E3A8A] focus:ring-[#1E3A8A]"
                                >
                                <span class="text-[11px] text-slate-700 leading-tight">
                                    <b>Sertakan coretan Tanda Tangan Digital Ketua/Wakil</b> di bawah Cap.
                                    <span class="text-slate-400 block font-normal">(Biarkan tidak tercentang jika draf PDF sudah memiliki tanda tangan manual/scan seperti surat asli).</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Sisi Kanan (5 Kolom): Interactive Mini A4 Paper Preview with Page Navigation -->
                <div class="lg:col-span-5 flex flex-col items-center justify-start p-3 bg-slate-100 rounded-lg border border-slate-200">
                    <div class="w-full flex items-center justify-between text-[11px] font-semibold text-slate-700 mb-2">
                        <span class="flex items-center space-x-1">
                            <svg class="w-3.5 h-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <span>Simulasi Kertas A4 (210×297 mm)</span>
                        </span>
                        <span class="text-[10px] text-slate-500 font-mono">Skala 1mm = 1px</span>
                    </div>

                    <!-- Navigation Bar Preview -->
                    <div class="w-full bg-white p-2 rounded border border-slate-200 mb-2.5 flex flex-col gap-1.5 shadow-2xs">
                        <div class="flex items-center justify-between gap-1 w-full">
                            <button
                                type="button"
                                @click="goToPage(currentPage - 1)"
                                :disabled="currentPage <= 1 || !pdfLoaded"
                                class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 disabled:opacity-40 disabled:cursor-not-allowed text-[11px] font-semibold text-slate-700 cursor-pointer transition flex items-center space-x-1 shrink-0 whitespace-nowrap"
                            >
                                <span>&larr;</span>
                                <span class="hidden sm:inline">Sebelumnya</span>
                            </button>

                            <div class="text-center px-1 shrink-0 whitespace-nowrap">
                                <div class="font-mono text-[11px] font-bold text-slate-800 whitespace-nowrap">
                                    Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages || 1"></span>
                                </div>
                                <div class="text-[9px] text-slate-400 whitespace-nowrap" x-show="!pdfLoaded">
                                    (Belum ada PDF)
                                </div>
                            </div>

                            <button
                                type="button"
                                @click="goToPage(currentPage + 1)"
                                :disabled="currentPage >= totalPages || !pdfLoaded"
                                class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 disabled:opacity-40 disabled:cursor-not-allowed text-[11px] font-semibold text-slate-700 cursor-pointer transition flex items-center space-x-1 shrink-0 whitespace-nowrap"
                            >
                                <span class="hidden sm:inline">Berikutnya</span>
                                <span>&rarr;</span>
                            </button>
                        </div>

                        <!-- Status Target Page vs Viewed Page -->
                        <div class="flex items-center justify-center pt-1 border-t border-slate-100">
                            <template x-if="currentPage === stamp_page">
                                <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Halaman Target Cap & TTD (Hal. <span x-text="stamp_page"></span>)</span>
                                </span>
                            </template>
                            <template x-if="currentPage !== stamp_page">
                                <button
                                    type="button"
                                    @click="setStampPage(currentPage)"
                                    class="inline-flex items-center space-x-1.5 px-3 py-1 rounded bg-blue-700 hover:bg-blue-800 text-white text-[11px] font-bold shadow-xs transition cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    <span>Pasang Cap & TTD di Halaman Ini (Hal. <span x-text="currentPage"></span>)</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Kertas A4 Mini Interaktif (210px x 297px) -->
                    <div
                        @click="onPaperClick($event)"
                        class="relative bg-white shadow-md border border-slate-300 rounded cursor-crosshair overflow-hidden select-none"
                        style="width: 210px; height: 297px;"
                        title="Klik di mana saja pada kertas ini untuk memindahkan Cap BEM!"
                    >
                        <!-- Canvas Pratinjau Dokumen PDF Asli yang Diunggah -->
                        <canvas
                            id="create-pdf-canvas"
                            class="absolute inset-0 block pointer-events-none z-0"
                            style="width: 100%; height: 100%;"
                            x-show="pdfLoaded"
                        ></canvas>

                        <!-- Indikator Loading Saat Merender Halaman -->
                        <div x-show="pdfLoading" class="absolute inset-0 bg-white/70 backdrop-blur-xs flex flex-col items-center justify-center z-30 pointer-events-none" style="display: none;">
                            <svg class="animate-spin h-6 w-6 text-blue-700 mb-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span class="text-[9px] font-semibold text-slate-700">Merender PDF...</span>
                        </div>

                        <!-- Garis Simulasi Placeholder (Tampil jika belum ada PDF diunggah) -->
                        <div x-show="!pdfLoaded" class="absolute inset-0 pointer-events-none z-0 bg-white">
                            <!-- Garis Simulasi Kop Surat -->
                            <div class="absolute top-3 left-4 right-4 h-1.5 bg-slate-200 rounded-xs"></div>
                            <div class="absolute top-6 left-8 right-8 h-1 bg-slate-200 rounded-xs"></div>
                            <div class="absolute top-8 left-4 right-4 h-0.5 bg-slate-400"></div>

                            <!-- Garis Simulasi Paragraf Surat -->
                            <div class="absolute top-12 left-4 right-4 h-1 bg-slate-100 rounded-xs"></div>
                            <div class="absolute top-15 left-4 right-10 h-1 bg-slate-100 rounded-xs"></div>
                            <div class="absolute top-18 left-4 right-4 h-1 bg-slate-100 rounded-xs"></div>
                            <div class="absolute top-22 left-4 right-6 h-1 bg-slate-100 rounded-xs"></div>
                            <div class="absolute top-25 left-4 right-14 h-1 bg-slate-100 rounded-xs"></div>
                            <div class="absolute top-30 left-4 right-4 h-1 bg-slate-100 rounded-xs"></div>

                            <!-- Zona Tanda Tangan Kiri (Simulasi) -->
                            <div class="absolute left-6 text-center" style="top: 180px; width: 65px;">
                                <div class="text-[8px] font-bold text-slate-400 leading-none">Ketua BEM</div>
                                <div class="h-6 border-b border-dashed border-slate-300 my-0.5 flex items-center justify-center">
                                    <span class="text-[7px] text-slate-300 italic">[Area TTD]</span>
                                </div>
                                <div class="text-[7px] text-slate-400 leading-none">Nama Ketua</div>
                            </div>

                            <!-- Zona Tanda Tangan Kanan (Simulasi) -->
                            <div class="absolute right-6 text-center" style="top: 180px; width: 65px;">
                                <div class="text-[8px] font-bold text-slate-400 leading-none">Ketua Panitia</div>
                                <div class="h-6 border-b border-dashed border-slate-300 my-0.5 flex items-center justify-center">
                                    <span class="text-[7px] text-slate-300 italic">[Area TTD]</span>
                                </div>
                                <div class="text-[7px] text-slate-400 leading-none">Nama Panitia</div>
                            </div>
                        </div>

                        <!-- Preview Cap BEM Stempel yang Bergerak Sesuai cap_x & cap_y (HANYA tampil jika currentPage === stamp_page) -->
                        <div
                            x-show="currentPage === stamp_page"
                            class="absolute transition-all duration-75 pointer-events-none flex flex-col items-center justify-center z-20"
                            :style="'left: ' + cap_x + 'px; top: ' + cap_y + 'px; width: ' + cap_width + 'px; height: ' + (cap_width / 1.777) + 'px;'"
                        >
                            <img
                                src="{{ asset('images/cap_bem.png') }}"
                                alt="Cap BEM"
                                class="w-full h-full object-contain opacity-90 drop-shadow-xs"
                            >
                            <div class="absolute -top-3.5 bg-blue-900 text-white font-mono text-[7px] px-1 rounded shadow-xs whitespace-nowrap">
                                Cap BEM (Hal. <span x-text="stamp_page"></span>)
                            </div>
                        </div>

                        <!-- Preview QR Code Footer di Ujung Bawah Sesuai qr_position -->
                        <div
                            x-show="currentPage === stamp_page"
                            class="absolute transition-all duration-150 pointer-events-none bg-white/95 border border-slate-300 rounded-xs p-0.5 flex items-center space-x-1 shadow-xs z-10"
                            :style="qr_position === 'bottom_left' ? 'left: 10px; bottom: 8px;' : (qr_position === 'bottom_right' ? 'right: 10px; bottom: 8px;' : 'left: 50%; transform: translateX(-50%); bottom: 8px;')"
                            style="width: 72px; height: 18px;"
                        >
                            <!-- Mini QR Icon -->
                            <div class="w-3.5 h-3.5 bg-slate-900 flex items-center justify-center text-white shrink-0">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M3 3h8v8H3zm2 2v4h4V5zm8-2h8v8h-8zm2 2v4h4V5zM3 13h8v8H3zm2 2v4h4v-4zm13-2h3v2h-3zm-5 0h2v3h-2zm2 3h3v2h-3zm-2 2h2v3h-2zm5-2h3v5h-3zm-2 3h2v2h-2z"/>
                                </svg>
                            </div>
                            <div class="leading-none text-[6px] text-slate-800">
                                <b class="block text-slate-900">QR E-SIGN</b>
                                <span class="text-[5px] text-slate-500 font-mono">Verifikasi Sah</span>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Error jika render PDF gagal -->
                    <div x-show="pdfError" class="w-full mt-2 p-1.5 bg-rose-50 border border-rose-200 rounded text-rose-700 text-[10px] text-center" style="display: none;">
                        <span x-text="pdfErrorMessage || 'Gagal memuat pratinjau PDF'"></span>
                    </div>

                    <!-- Petunjuk Klik -->
                    <div class="text-[10px] text-slate-500 mt-2 text-center">
                        <template x-if="currentPage === stamp_page">
                            <span>
                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse mr-1"></span>
                                <b>Klik pada kertas di atas</b> untuk memindahkan Cap BEM di Halaman <span x-text="stamp_page"></span>!
                            </span>
                        </template>
                        <template x-if="currentPage !== stamp_page">
                            <span class="text-blue-900 font-medium">
                                <span class="inline-block w-2 h-2 rounded-full bg-blue-600 mr-1"></span>
                                Klik tombol di atas untuk memasang Cap BEM & TTD di Halaman <span x-text="currentPage"></span>.
                            </span>
                        </template>
                    </div>
                </div>
            </div>

            <!-- PDF Upload Box -->
            <div class="pt-4 border-t border-slate-100">
                <label class="block font-semibold text-slate-700 mb-1 text-xs">
                    File Draf PDF Asli <span class="text-rose-600">*</span>
                </label>
                <div class="border-2 border-dashed border-slate-300 hover:border-blue-600 rounded-lg p-6 text-center bg-slate-50 transition cursor-pointer" @click="$refs.pdfInput.click()">
                    <input
                        type="file"
                        x-ref="pdfInput"
                        name="draft_pdf"
                        accept="application/pdf"
                        required
                        class="hidden"
                        @change="onFileChosen($event)"
                    >
                    <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div class="text-xs font-semibold text-slate-700">Pilih berkas PDF draf (Maksimal 10MB)</div>
                    <div class="text-[11px] text-slate-400 mt-1">Dokumen akan tersimpan aman di direktori privat server</div>
                    <div x-show="uploadedFileName" class="mt-2 text-xs font-mono font-semibold text-blue-900 bg-white inline-block px-3 py-1 rounded border border-blue-200" x-text="'File: ' + uploadedFileName"></div>
                </div>
            </div>
        </div>

        <!-- 3. DYNAMIC RECIPIENTS FORM (Alpine.js) -->
        <div class="card-clean rounded-lg p-6">
            <div class="flex items-center justify-between pb-2 mb-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-[#0F172A] flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-[#0F172A] text-white text-xs flex items-center justify-center font-mono">3</span>
                    <span>Daftar Mahasiswa Resmi yang Tercantum (Anti-Titip Nama)</span>
                </h2>
                <button
                    type="button"
                    @click="addRecipient()"
                    class="px-3 py-1.5 rounded bg-blue-50 hover:bg-blue-100 text-blue-900 text-xs font-semibold border border-blue-200 transition cursor-pointer flex items-center space-x-1"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Tambah Mahasiswa</span>
                </button>
            </div>

            <p class="text-xs text-slate-500 mb-4">
                Nama dan NIM yang dimasukkan di sini akan dicatat di database untuk memverifikasi siapa saja yang berhak atas izin/tugas ini saat QR code di-scan pihak kampus.
            </p>

            <div class="space-y-3">
                <template x-for="(recipient, index) in recipients" :key="index">
                    <div class="p-3 bg-slate-50 rounded border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center text-xs">
                        <div class="sm:col-span-1 text-center font-mono font-bold text-slate-400" x-text="index + 1"></div>
                        <div class="sm:col-span-4">
                            <input
                                type="text"
                                :name="'recipients[' + index + '][student_name]'"
                                x-model="recipient.student_name"
                                placeholder="Nama Lengkap Mahasiswa"
                                required
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded text-xs focus:ring-1 focus:ring-blue-800"
                            >
                        </div>
                        <div class="sm:col-span-3">
                            <input
                                type="text"
                                :name="'recipients[' + index + '][nim]'"
                                x-model="recipient.nim"
                                placeholder="NIM (misal: 2310511045)"
                                required
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded text-xs font-mono focus:ring-1 focus:ring-blue-800"
                            >
                        </div>
                        <div class="sm:col-span-3">
                            <input
                                type="text"
                                :name="'recipients[' + index + '][notes]'"
                                x-model="recipient.notes"
                                placeholder="Peran/Keterangan (opsional)"
                                class="w-full px-2.5 py-1.5 bg-white border border-slate-300 rounded text-xs focus:ring-1 focus:ring-blue-800"
                            >
                        </div>
                        <div class="sm:col-span-1 text-right">
                            <button
                                type="button"
                                @click="removeRecipient(index)"
                                class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded transition"
                                title="Hapus Mahasiswa"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </template>

                <div x-show="recipients.length === 0" class="py-4 text-center text-xs text-slate-400 bg-slate-50/50 rounded border border-dashed border-slate-200">
                    Belum ada mahasiswa ditambahkan. Klik tombol <b>+ Tambah Mahasiswa</b> di atas.
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2 sm:gap-3">
            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-4 py-2.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold text-center transition">
                Batal
            </a>
            <button
                type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition min-h-[44px] flex items-center justify-center space-x-1.5 cursor-pointer"
            >
                <span>Ajukan ke Ketua / Wakil Ketua BEM</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Pastikan worker PDF.js menggunakan path relatif lokal domain yang sama
if (window.pdfjsLib) {
    window.pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';
}

function letterCreateForm() {
    // Simpan instance raw PDF.js di luar reactive proxy Alpine agar private members tidak rusak
    let rawPdfDoc = null;
    let activeRenderTask = null;

    return {
        uploadedFileName: '',
        pdfLoaded: false,
        pdfLoading: false,
        pdfError: false,
        pdfErrorMessage: '',
        currentPage: 1,
        totalPages: 1,

        qr_position: '{{ old('qr_position', 'bottom_left') }}',
        cap_preset: 'ttd_kiri',
        cap_x: {{ old('cap_x', 28) }},
        cap_y: {{ old('cap_y', 195) }},
        cap_width: {{ old('cap_width', 38) }},
        include_signature_img: {{ old('include_signature_img') ? 'true' : 'false' }},
        stamp_page: {{ old('stamp_page', 1) }},
        recipients: [
            { student_name: '', nim: '', notes: 'Delegasi Mahasiswa' }
        ],

        async onFileChosen(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.uploadedFileName = file.name;
            this.pdfLoading = true;
            this.pdfError = false;
            this.pdfErrorMessage = '';

            try {
                let attempts = 0;
                while (!window.pdfjsLib && attempts < 40) {
                    await new Promise(r => setTimeout(r, 50));
                    attempts++;
                }
                if (!window.pdfjsLib) {
                    throw new Error("Library PDF.js belum siap dimuat");
                }

                pdfjsLib.GlobalWorkerOptions.workerSrc = '/js/pdf.worker.min.js';

                // Baca file menggunakan arrayBuffer dan Uint8Array untuk keandalan maksimal
                const arrayBuffer = await file.arrayBuffer();
                const uint8Array = new Uint8Array(arrayBuffer);

                const loadingTask = pdfjsLib.getDocument({
                    data: uint8Array
                });

                rawPdfDoc = await loadingTask.promise;
                this.totalPages = rawPdfDoc.numPages;
                this.pdfLoaded = true;

                // Set tampilan awal ke Halaman 1
                this.currentPage = 1;

                // Validasi dan sesuaikan nilai stamp_page agar berada di range 1 s/d totalPages
                if (!this.stamp_page || this.stamp_page < 1 || this.stamp_page > this.totalPages) {
                    this.stamp_page = 1;
                }

                await this.$nextTick();
                await this.goToPage(1);
            } catch (err) {
                console.error("Gagal membaca atau memproses berkas draf PDF:", err);
                this.pdfError = true;
                this.pdfErrorMessage = err?.message || 'Gagal membaca berkas PDF';
            } finally {
                this.pdfLoading = false;
            }
        },

        async goToPage(pageNo) {
            if (!rawPdfDoc) return;
            pageNo = Math.max(1, Math.min(this.totalPages, pageNo));
            this.currentPage = pageNo;

            // Batalkan tugas render kanvas sebelumnya jika masih berjalan
            if (activeRenderTask) {
                try {
                    activeRenderTask.cancel();
                } catch (_) {}
                activeRenderTask = null;
            }

            try {
                await this.$nextTick();
                const page = await rawPdfDoc.getPage(pageNo);
                const canvas = document.getElementById('create-pdf-canvas');
                if (!canvas) return;

                const unscaled = page.getViewport({ scale: 1.0 });
                // Target lebar kanvas adalah 210px (A4 scale 1mm = 1px)
                // Menggunakan DPR 2x agar teks pratinjau tajam dan terbaca jelas
                const targetWidth = 210;
                const dpr = Math.min(window.devicePixelRatio || 1, 2);
                const scale = (targetWidth / unscaled.width) * dpr;
                const viewport = page.getViewport({ scale: scale });

                canvas.width = Math.floor(viewport.width);
                canvas.height = Math.floor(viewport.height);
                canvas.style.display = 'block';
                canvas.style.width = '100%';
                canvas.style.height = '100%';

                const context = canvas.getContext('2d');
                context.fillStyle = '#FFFFFF';
                context.fillRect(0, 0, canvas.width, canvas.height);

                activeRenderTask = page.render({
                    canvasContext: context,
                    viewport: viewport
                });
                await activeRenderTask.promise;
                activeRenderTask = null;
                this.pdfError = false;
            } catch (e) {
                if (e && e.name === 'RenderingCancelledException') return;
                console.error("Gagal merender halaman preview PDF:", e);
                this.pdfError = true;
                this.pdfErrorMessage = 'Gagal merender halaman: ' + (e?.message || 'Error');
            }
        },

        setStampPage(pageNo) {
            const target = Math.max(1, Math.min(this.totalPages || 1, pageNo));
            this.stamp_page = target;
            if (this.currentPage !== this.stamp_page) {
                this.goToPage(this.stamp_page);
            }
        },

        onStampPageInputChange() {
            let val = parseInt(this.stamp_page) || 1;
            val = Math.max(1, Math.min(this.totalPages || 1, val));
            this.stamp_page = val;
            this.goToPage(this.stamp_page);
        },

        applyPreset(preset) {
            this.cap_preset = preset;
            if (preset === 'ttd_kiri') {
                this.cap_x = 28;
                this.cap_y = 195;
                this.cap_width = 38;
            } else if (preset === 'ttd_kanan') {
                this.cap_x = 125;
                this.cap_y = 195;
                this.cap_width = 38;
            } else if (preset === 'kanan_bawah') {
                this.cap_x = 135;
                this.cap_y = 230;
                this.cap_width = 38;
            } else if (preset === 'kiri_bawah') {
                this.cap_x = 20;
                this.cap_y = 230;
                this.cap_width = 38;
            }
        },

        onPaperClick(event) {
            // Jika sedang melihat halaman yang berbeda dari stamp_page, jadikan halaman ini sebagai target
            if (this.currentPage !== this.stamp_page) {
                this.stamp_page = this.currentPage;
            }

            const rect = event.currentTarget.getBoundingClientRect();
            const clickX = event.clientX - rect.left;
            const clickY = event.clientY - rect.top;

            // Center the cap on the click point
            const halfW = this.cap_width / 2;
            const halfH = (this.cap_width / 1.777) / 2;

            let newX = Math.round(clickX - halfW);
            let newY = Math.round(clickY - halfH);

            newX = Math.max(5, Math.min(210 - this.cap_width - 5, newX));
            newY = Math.max(10, Math.min(297 - 35, newY));

            this.cap_x = newX;
            this.cap_y = newY;
            this.cap_preset = 'custom';
        },

        addRecipient() {
            this.recipients.push({ student_name: '', nim: '', notes: '' });
        },

        removeRecipient(index) {
            this.recipients.splice(index, 1);
        }
    };
}
</script>
@endpush
