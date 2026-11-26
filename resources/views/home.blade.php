@extends('layouts.app')

@section('title', 'Verifikasi Keaslian Dokumen & Surat Resmi')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="homeVerification()">

    <!-- Official Institutional Header Box -->
    <div class="card-clean rounded-lg p-6 sm:p-8 mb-8 border-l-4 border-l-[#1E3A8A]">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start space-x-4">
                <div class="w-14 h-14 rounded bg-[#0F172A] border border-slate-700 flex items-center justify-center font-bold text-white tracking-widest text-lg shrink-0">
                    FIKOM
                </div>
                <div>
                    <div class="text-xs font-bold uppercase tracking-wider text-blue-900 font-mono mb-1">
                        Portal Layanan Pengesahan Digital Terpadu
                    </div>
                    <h1 class="text-xl sm:text-2xl font-bold text-[#0F172A] leading-snug">
                        Verifikasi Keaslian Dokumen & Surat Resmi BEM FIKOM
                    </h1>
                    <p class="text-sm text-slate-600 mt-1 max-w-2xl">
                        Sistem resmi pengujian validitas stempel elektronik, sertifikat kriptografi X.509, dan keutuhan sidik jari SHA-256 surat resmi Badan Eksekutif Mahasiswa Fakultas Ilmu Komputer.
                    </p>
                </div>
            </div>

            <!-- Quick Action: Login Pengurus -->
            <div class="shrink-0 flex md:flex-col justify-end gap-2 border-t md:border-t-0 md:border-l border-slate-200 pt-4 md:pt-0 md:pl-6">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-semibold transition">
                        <span>Buka Dashboard</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center space-x-2 px-4 py-2 rounded bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold border border-slate-300 transition">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>Login Pengurus</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Verification Box (Tabs: Nomor Surat & Uji Integritas File PDF) -->
    <div class="card-clean rounded-lg overflow-hidden">
        <!-- Navigation Tab Header -->
        <div class="flex border-b border-slate-200 bg-slate-50">
            <button
                type="button"
                @click="activeTab = 'query'"
                :class="activeTab === 'query' ? 'bg-white border-b-2 border-b-[#1E3A8A] text-[#0F172A] font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="flex-1 py-3.5 px-4 text-center text-sm sm:text-base flex items-center justify-center space-x-2 transition cursor-pointer"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Cari Nomor / Kode Surat</span>
            </button>

            <button
                type="button"
                @click="activeTab = 'integrity'"
                :class="activeTab === 'integrity' ? 'bg-white border-b-2 border-b-[#1E3A8A] text-[#0F172A] font-semibold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="flex-1 py-3.5 px-4 text-center text-sm sm:text-base flex items-center justify-center space-x-2 transition cursor-pointer"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Uji Integritas File PDF (Anti-Edit)</span>
                <span class="bg-blue-100 text-blue-800 text-[10px] px-1.5 py-0.5 rounded font-mono font-bold uppercase">SHA-256</span>
            </button>
        </div>

        <!-- TAB 1: Cek Kode / Nomor Surat -->
        <div x-show="activeTab === 'query'" class="p-6 sm:p-8">
            <h2 class="text-base font-bold text-[#0F172A] mb-1">Pemeriksaan Surat Berdasarkan Nomor atau Token</h2>
            <p class="text-xs text-slate-500 mb-6">
                Masukkan nomor surat resmi lengkap (misal: <code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded font-mono">014/BEM-FIKOM/XI/2026</code>) atau 36 karakter UUID token verifikasi yang tertera di bawah QR Code dokumen.
            </p>

            @if(session('query_error'))
                <div class="mb-4 bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded text-sm flex items-start space-x-2">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>{{ session('query_error') }}</div>
                </div>
            @endif

            <form action="{{ route('verify.query') }}" method="POST" class="max-w-2xl">
                @csrf
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            name="letter_query"
                            value="{{ old('letter_query') }}"
                            placeholder="Contoh: 014/BEM-FIKOM/XI/2026 atau Token UUID"
                            required
                            class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-300 rounded text-sm font-mono focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] focus:border-transparent transition"
                        >
                    </div>
                    <button
                        type="submit"
                        class="px-6 py-2.5 bg-[#0F172A] hover:bg-slate-800 text-white rounded text-sm font-semibold transition shrink-0 flex items-center justify-center space-x-2"
                    >
                        <span>Cek Keabsahan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- TAB 2: Uji Integritas File PDF (Anti-Edit / Drag & Drop) -->
        <div x-show="activeTab === 'integrity'" class="p-6 sm:p-8">
            <h2 class="text-base font-bold text-[#0F172A] mb-1">Uji Integritas File PDF Digital (Anti-Manipulasi)</h2>
            <p class="text-xs text-slate-500 mb-6">
                Unggah file PDF surat yang diserahkan oleh mahasiswa. Sistem akan menghitung sidik jari kriptografi SHA-256 asli dari file dan mencocokkannya dengan arsip database BEM FIKOM. File yang pernah diedit teksnya, dipotong, atau di-reexport akan langsung terdeteksi tidak sah.
            </p>

            <!-- Drag & Drop Upload Area -->
            <div
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleFileDrop($event)"
                :class="isDragging ? 'border-blue-600 bg-blue-50/50' : 'border-slate-300 bg-slate-50/70 hover:bg-slate-50'"
                class="border-2 border-dashed rounded-lg p-8 text-center transition cursor-pointer max-w-3xl mx-auto"
                @click="$refs.fileInput.click()"
            >
                <input
                    type="file"
                    x-ref="fileInput"
                    @change="handleFileSelect($event)"
                    accept="application/pdf"
                    class="hidden"
                >

                <div class="flex flex-col items-center justify-center space-y-3">
                    <div class="w-12 h-12 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>

                    <div>
                        <div class="text-sm font-semibold text-[#0F172A]">
                            Tarik & Lepas File PDF ke Sini, atau <span class="text-blue-700 underline">Pilih File</span>
                        </div>
                        <div class="text-xs text-slate-500 mt-1">
                            Format wajib .PDF asli (Maksimal 10MB) &bull; Komputasi SHA-256 Otomatis
                        </div>
                    </div>

                    <div x-show="fileName" class="text-xs font-mono bg-white px-3 py-1.5 rounded border border-slate-200 text-slate-700 font-semibold" x-text="'File terpilih: ' + fileName"></div>
                </div>
            </div>

            <!-- Loading Spinner Indicator -->
            <div x-show="isLoading" class="mt-6 text-center py-4">
                <div class="inline-flex items-center space-x-2 text-sm text-slate-700 font-medium">
                    <svg class="animate-spin h-5 w-5 text-[#1E3A8A]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Menghitung Hash Kriptografi SHA-256 & Memvalidasi ke Database...</span>
                </div>
            </div>

            <!-- RESULT: VALID (MATCH) -->
            <div x-show="checkResult && checkResult.match" class="mt-6 card-clean rounded-lg p-6 bg-emerald-50/60 border border-emerald-300 max-w-3xl mx-auto">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <div class="text-xs font-mono font-bold text-emerald-800 uppercase tracking-wider">Hasil Verifikasi Kriptografis</div>
                        <h3 class="text-base font-bold text-emerald-900 mt-0.5">DOKUMEN ASLI & TERVERIFIKASI (100% UTUH)</h3>
                        <p class="text-xs text-emerald-800 mt-1" x-text="checkResult.message"></p>

                        <div class="mt-4 bg-white rounded border border-emerald-200 p-4 space-y-2 text-xs">
                            <div class="flex justify-between border-b border-slate-100 pb-1.5">
                                <span class="text-slate-500">Nomor Surat:</span>
                                <span class="font-mono font-bold text-slate-900" x-text="checkResult.letter?.letter_number"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-1.5">
                                <span class="text-slate-500">Perihal:</span>
                                <span class="font-semibold text-slate-900 text-right" x-text="checkResult.letter?.title"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-1.5">
                                <span class="text-slate-500">Kategori / Tanggal:</span>
                                <span class="text-slate-800" x-text="(checkResult.letter?.category_label || '') + ' &bull; ' + (checkResult.letter?.letter_date || '')"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-1.5">
                                <span class="text-slate-500">Disahkan Oleh:</span>
                                <span class="font-semibold text-blue-900" x-text="(checkResult.letter?.approved_by || '-') + ' (' + (checkResult.letter?.approved_by_jabatan || '-') + ')'"></span>
                            </div>
                            <div class="flex justify-between border-b border-slate-100 pb-1.5">
                                <span class="text-slate-500">Waktu Pengesahan:</span>
                                <span class="font-mono text-slate-800" x-text="checkResult.letter?.approved_at"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block mb-1">SHA-256 Fingerprint:</span>
                                <div class="bg-slate-50 p-2 rounded border border-slate-200 font-mono text-[11px] text-slate-700 break-all select-all" x-text="checkResult.hash"></div>
                            </div>
                        </div>

                        <!-- Recipients in Letter -->
                        <div class="mt-4" x-show="checkResult.recipients && checkResult.recipients.length > 0">
                            <div class="text-xs font-bold text-slate-800 mb-2">Daftar Mahasiswa yang Sah Tercantum di Surat Ini:</div>
                            <div class="overflow-x-auto border border-emerald-200 rounded bg-white">
                                <table class="min-w-full text-xs text-left">
                                    <thead class="bg-slate-50 border-b border-slate-200 font-semibold text-slate-700">
                                        <tr>
                                            <th class="py-1.5 px-3">Nama Mahasiswa</th>
                                            <th class="py-1.5 px-3">NIM</th>
                                            <th class="py-1.5 px-3">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <template x-for="r in checkResult.recipients" :key="r.nim">
                                            <tr>
                                                <td class="py-1.5 px-3 font-semibold text-slate-900" x-text="r.student_name"></td>
                                                <td class="py-1.5 px-3 font-mono text-slate-700" x-text="r.nim"></td>
                                                <td class="py-1.5 px-3 text-slate-500" x-text="r.notes || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <a :href="checkResult.letter?.verify_url" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold transition">
                                <span>Buka Halaman Verifikasi Lengkap</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RESULT: INVALID (MISMATCH) -->
            <div x-show="checkResult && !checkResult.match" class="mt-6 card-clean rounded-lg p-6 bg-rose-50/70 border border-rose-300 max-w-3xl mx-auto">
                <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-mono font-bold text-rose-800 uppercase tracking-wider">Peringatan Integritas Dokumen</div>
                        <h3 class="text-base font-bold text-rose-900 mt-0.5">DOKUMEN TIDAK TERDAFTAR ATAU TELAH DIMODIFIKASI</h3>
                        <p class="text-xs text-rose-800 mt-1" x-text="checkResult.message"></p>

                        <div class="mt-3 bg-white p-3 rounded border border-rose-200">
                            <span class="text-[11px] text-slate-500 block mb-1">Sidik Jari SHA-256 File yang Diunggah:</span>
                            <div class="font-mono text-[11px] text-rose-700 break-all select-all font-semibold" x-text="checkResult.hash"></div>
                        </div>

                        <div class="mt-3 text-xs text-rose-800 bg-rose-100/50 p-2.5 rounded border border-rose-200/60">
                            <b>Pemberitahuan untuk Dosen/Pihak Kampus:</b> Jangan menerima dokumen ini. Minta mahasiswa yang bersangkutan untuk mengunduh ulang file PDF asli dari sistem atau menunjukkan scan QR Code langsung di depan Anda.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Institutional Guidance Information -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card-clean rounded-lg p-5">
            <div class="w-8 h-8 rounded bg-slate-100 text-slate-700 flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-[#0F172A]">1. Scan QR Code Dokumen</h3>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                Arahkan kamera smartphone ke QR Code yang dicap di surat resmi. Anda akan diarahkan ke halaman verifikasi resmi BEM FIKOM.
            </p>
        </div>

        <div class="card-clean rounded-lg p-5">
            <div class="w-8 h-8 rounded bg-slate-100 text-slate-700 flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-[#0F172A]">2. Cek Daftar Mahasiswa</h3>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                Cocokkan Nama dan NIM mahasiswa yang mengajukan surat dengan tabel penerima resmi untuk mencegah manipulasi titip nama.
            </p>
        </div>

        <div class="card-clean rounded-lg p-5">
            <div class="w-8 h-8 rounded bg-slate-100 text-slate-700 flex items-center justify-center mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
            </div>
            <h3 class="text-sm font-bold text-[#0F172A]">3. Validasi Hash SHA-256</h3>
            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                Gunakan tab "Uji Integritas File PDF" di atas untuk memastikan berkas PDF belum pernah dimodifikasi bahkan 1 karakter pun.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function homeVerification() {
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
