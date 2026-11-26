@extends('layouts.app')

@section('title', 'Verifikasi: ' . $letter->letter_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="verifyPage()">

    <!-- Official Header with Logo -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/logo_bem_fikom.png') }}" alt="Logo BEM FIKOM" class="h-12 w-auto object-contain">
        </div>
        @auth
            <a href="{{ route('dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">
                &larr; Kembali ke Dashboard
            </a>
        @else
            <a href="{{ route('verify.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">
                &larr; Cek Nomor / Dokumen Lain
            </a>
        @endauth
    </div>

    <!-- Flash notification if redirected from home integrity test -->
    @if(session('integrity_success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-300 text-emerald-900 px-4 py-3 rounded text-sm flex items-start space-x-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>{{ session('integrity_success') }}</div>
        </div>
    @endif

    <!-- 1. BIG STATUS BANNER -->
    @if($letter->isApproved())
        <div class="card-clean rounded-lg p-6 bg-emerald-50/70 border-2 border-emerald-400 mb-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="w-14 h-14 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-mono font-bold text-emerald-800 uppercase tracking-wider">Status Dokumen Resmi BEM FIKOM</div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-emerald-950 mt-0.5">SURAT RESMI SAH & TERDAFTAR</h1>
                    <p class="text-xs text-emerald-800 mt-1 max-w-xl">
                        Dokumen ini telah disahkan secara elektronik dengan stempel digital resmi BEM Fakultas Ilmu Komputer dan tersertifikasi kriptografi digital X.509 yang valid.
                    </p>
                </div>
            </div>
        </div>
    @elseif($letter->isRevoked())
        <div class="card-clean rounded-lg p-6 bg-rose-50/80 border-2 border-rose-500 mb-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="w-14 h-14 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-mono font-bold text-rose-800 uppercase tracking-wider">Status Dokumen Resmi</div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-rose-950 mt-0.5">PENGESAHAN SURAT TELAH DICABUT</h1>
                    <p class="text-xs text-rose-800 mt-1">
                        Surat resmi ini sebelumnya pernah disahkan namun telah <b>DICABUT / DIBATALKAN</b> oleh pimpinan BEM FIKOM. Dokumen ini tidak lagi berlaku untuk keperluan apapun.
                    </p>
                    @if($letter->rejection_or_revoke_reason)
                        <div class="mt-3 bg-white/90 p-3 rounded border border-rose-300 text-xs text-rose-900">
                            <b>Alasan Pencabutan Resmi:</b> {{ $letter->rejection_or_revoke_reason }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif($letter->isRejected())
        <div class="card-clean rounded-lg p-6 bg-rose-50/80 border-2 border-rose-400 mb-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="w-14 h-14 rounded-full bg-rose-600 text-white flex items-center justify-center shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-mono font-bold text-rose-800 uppercase tracking-wider">Status Dokumen</div>
                    <h1 class="text-xl sm:text-2xl font-bold text-rose-950 mt-0.5">PENGAJUAN SURAT DITOLAK</h1>
                    <p class="text-xs text-rose-800 mt-1">
                        Draf surat ini telah ditolak oleh pimpinan BEM FIKOM dan tidak memiliki keabsahan resmi.
                    </p>
                    @if($letter->rejection_or_revoke_reason)
                        <div class="mt-3 bg-white p-3 rounded border border-rose-200 text-xs text-rose-900">
                            <b>Alasan Penolakan:</b> {{ $letter->rejection_or_revoke_reason }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="card-clean rounded-lg p-6 bg-amber-50/80 border-2 border-amber-400 mb-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start text-center sm:text-left space-y-3 sm:space-y-0 sm:space-x-4">
                <div class="w-14 h-14 rounded-full bg-amber-500 text-slate-900 flex items-center justify-center shrink-0 font-bold text-xl font-mono">
                    !
                </div>
                <div>
                    <div class="text-xs font-mono font-bold text-amber-800 uppercase tracking-wider">Status Dokumen</div>
                    <h1 class="text-xl sm:text-2xl font-bold text-amber-950 mt-0.5">SURAT MENUNGGU PENGESAHAN</h1>
                    <p class="text-xs text-amber-800 mt-1">
                        Draf surat ini telah diajukan oleh Sekretariat BEM FIKOM namun belum ditandatangani secara digital oleh Ketua atau Wakil Ketua BEM.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- 2. METADATA SURAT & INFORMASI PENGESAHAN (2 Cols) -->
        <div class="md:col-span-2 space-y-6">
            <!-- Metadata Card -->
            <div class="card-clean rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-[#0F172A] flex items-center space-x-2">
                        <svg class="w-4 h-4 text-[#1E3A8A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Informasi Dokumen Resmi</span>
                    </h2>
                    <span class="text-xs font-mono bg-blue-100 text-blue-900 px-2 py-0.5 rounded font-semibold">
                        {{ $letter->category_label }}
                    </span>
                </div>

                <div class="p-6 divide-y divide-slate-100 text-xs">
                    <div class="py-2.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                        <span class="text-slate-500 font-medium sm:w-1/3">Nomor Surat Resmi</span>
                        <span class="font-mono font-bold text-slate-900 sm:w-2/3 text-sm select-all">{{ $letter->letter_number }}</span>
                    </div>

                    <div class="py-2.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                        <span class="text-slate-500 font-medium sm:w-1/3">Perihal Dokumen</span>
                        <span class="font-semibold text-slate-800 sm:w-2/3">{{ $letter->title }}</span>
                    </div>

                    <div class="py-2.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                        <span class="text-slate-500 font-medium sm:w-1/3">Dasar Kegiatan</span>
                        <span class="text-slate-700 sm:w-2/3">{{ $letter->event_name }}</span>
                    </div>

                    <div class="py-2.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                        <span class="text-slate-500 font-medium sm:w-1/3">Tanggal Surat Diterbitkan</span>
                        <span class="text-slate-800 sm:w-2/3">{{ $letter->letter_date->translatedFormat('d F Y') }}</span>
                    </div>

                    <div class="py-2.5 flex flex-col sm:flex-row sm:justify-between gap-1">
                        <span class="text-slate-500 font-medium sm:w-1/3">Diajukan Oleh</span>
                        <span class="text-slate-700 sm:w-2/3">{{ $letter->submitter?->name }} ({{ $letter->submitter?->jabatan }})</span>
                    </div>
                </div>
            </div>

            <!-- Approval Info Card (If approved/revoked) -->
            @if($letter->approved_by && $letter->approved_at)
                <div class="card-clean rounded-lg overflow-hidden border-t-2 border-t-[#1E3A8A]">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                        <h2 class="text-sm font-bold text-[#0F172A] flex items-center space-x-2">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Otoritas Penandatangan & Kriptografi</span>
                        </h2>
                    </div>

                    <div class="p-6 space-y-4 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <span class="text-slate-500 block">Pejabat Pengesah:</span>
                                <span class="font-bold text-slate-900 text-sm block mt-0.5">{{ $letter->approver?->name }}</span>
                                <span class="text-blue-900 font-medium">{{ $letter->approver?->jabatan }}</span>
                            </div>

                            <div>
                                <span class="text-slate-500 block">Waktu Penandatanganan:</span>
                                <span class="font-mono text-slate-800 font-semibold block mt-0.5">
                                    {{ $letter->approved_at->format('d/m/Y - H:i:s') }} WIB
                                </span>
                                <span class="text-emerald-700 font-medium">Stempel & TTD Digital Aktif</span>
                            </div>
                        </div>

                        <!-- SHA-256 Hash Display with Copy Button -->
                        @if($letter->file_hash)
                            <div class="pt-3 border-t border-slate-100">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-slate-500 font-medium">Sidik Jari Digital Dokumen (SHA-256):</span>
                                    <button
                                        type="button"
                                        @click="copyHash('{{ $letter->file_hash }}')"
                                        class="text-[11px] font-mono text-blue-800 hover:text-blue-950 font-semibold underline flex items-center space-x-1 cursor-pointer"
                                    >
                                        <span x-text="copied ? 'Tersalin!' : 'Salin Hash Lengkap'"></span>
                                    </button>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded border border-slate-200 font-mono text-[11px] text-slate-700 break-all select-all">
                                    {{ $letter->file_hash }}
                                </div>
                                <div class="mt-1 text-[10px] text-slate-400">
                                    * Setiap perubahan karakter atau halaman pada PDF akan menghasilkan nilai hash yang sama sekali berbeda.
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 3. OFFICIAL RECIPIENTS TABLE (Mencegah Titip Nama) -->
            <div class="card-clean rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-[#0F172A] flex items-center space-x-2">
                            <svg class="w-4 h-4 text-[#1E3A8A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span>Daftar Mahasiswa Resmi yang Tercantum</span>
                        </h2>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            Nama dan NIM di bawah ini terdaftar resmi dalam SK/Surat ini di database BEM FIKOM.
                        </div>
                    </div>
                    <span class="text-xs bg-slate-200 text-slate-700 px-2 py-0.5 rounded font-mono font-semibold">
                        {{ $letter->recipients->count() }} Orang
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                        <thead class="bg-slate-50/70 font-semibold text-slate-700">
                            <tr>
                                <th class="py-2.5 px-4 w-12 text-center">No</th>
                                <th class="py-2.5 px-4">Nama Lengkap Mahasiswa</th>
                                <th class="py-2.5 px-4">NIM</th>
                                <th class="py-2.5 px-4">Keterangan / Peran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($letter->recipients as $idx => $recipient)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="py-2.5 px-4 text-center font-mono text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-4 font-semibold text-slate-900">{{ $recipient->student_name }}</td>
                                    <td class="py-2.5 px-4 font-mono text-slate-700">{{ $recipient->nim }}</td>
                                    <td class="py-2.5 px-4 text-slate-600">{{ $recipient->notes ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-slate-400">
                                        Tidak ada daftar nama mahasiswa individu yang dilampirkan khusus pada surat ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SIDEBAR: WIDGET UJI PDF DI TEMPAT + DOWNLOAD RESMI (1 Col) -->
        <div class="space-y-6">
            <!-- Official Actions -->
            @if($letter->isApproved())
                <div class="card-clean rounded-lg p-5 bg-[#0F172A] text-white">
                    <h3 class="text-sm font-bold text-white mb-2">Unduh PDF Resmi</h3>
                    <p class="text-xs text-slate-300 mb-4 leading-relaxed">
                        Salinan resmi dokumen PDF bertanda tangan digital dan stempel resmi BEM FIKOM.
                    </p>
                    <a
                        href="{{ route('verify.download', $letter->verification_token) }}"
                        class="w-full inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded bg-[#1E3A8A] hover:bg-blue-800 text-white text-xs font-semibold border border-blue-500/40 transition shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Unduh PDF Resmi (.pdf)</span>
                    </a>
                </div>
            @endif

            <!-- Mini Widget: Uji File PDF Terhadap Surat Ini -->
            <div class="card-clean rounded-lg p-5">
                <h3 class="text-sm font-bold text-[#0F172A] mb-1 flex items-center space-x-1.5">
                    <svg class="w-4 h-4 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Uji File PDF di Sini</span>
                </h3>
                <p class="text-xs text-slate-500 mb-4 leading-relaxed">
                    Pihak Dosen/Kampus dapat mengunggah file PDF yang diserahkan mahasiswa untuk memastikan berkas tersebut 100% cocok dengan surat ini.
                </p>

                <div
                    @click="$refs.miniFileInput.click()"
                    class="border-2 border-dashed border-slate-300 hover:border-blue-600 rounded-lg p-4 text-center cursor-pointer bg-slate-50 hover:bg-blue-50/30 transition"
                >
                    <input
                        type="file"
                        x-ref="miniFileInput"
                        @change="testLocalPdf($event)"
                        accept="application/pdf"
                        class="hidden"
                    >
                    <svg class="w-6 h-6 text-slate-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <div class="text-xs font-semibold text-slate-700">Klik untuk Pilih File PDF</div>
                    <div class="text-[10px] text-slate-400 mt-0.5">Maks 10MB</div>
                </div>

                <!-- Mini Testing Status -->
                <div x-show="testingFile" class="mt-3 text-xs text-center text-slate-600 font-medium">
                    <span class="animate-pulse">Sedang mencocokkan SHA-256...</span>
                </div>

                <!-- Mini Test Result -->
                <div x-show="testResult !== null" class="mt-3">
                    <div x-show="testResult === true" class="p-3 rounded bg-emerald-50 border border-emerald-300 text-xs text-emerald-900">
                        <b>HASIL COCOK (100% ASLI):</b> Berkas PDF yang Anda uji identik tanpa ada perubahan dengan surat resmi ini.
                    </div>
                    <div x-show="testResult === false" class="p-3 rounded bg-rose-50 border border-rose-300 text-xs text-rose-900">
                        <b>PERINGATAN: TIDAK COCOK!</b> File PDF yang Anda uji BUKAN salinan asli dari surat ini atau teks di dalamnya telah diubah.
                    </div>
                </div>
            </div>

            <!-- Token UUID Info -->
            <div class="card-clean rounded-lg p-5 text-xs text-slate-600 space-y-2">
                <div class="font-bold text-slate-800">Token Verifikasi Permanen:</div>
                <div class="font-mono text-[11px] bg-slate-100 p-2 rounded text-slate-700 break-all select-all">
                    {{ $letter->verification_token }}
                </div>
                <div class="text-[10px] text-slate-400">
                    URL QR Code ini permanen dan dapat dicek kapan saja dari peramban desktop maupun ponsel.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function verifyPage() {
    return {
        copied: false,
        testingFile: false,
        testResult: null,
        targetHash: '{{ $letter->file_hash }}',

        copyHash(text) {
            navigator.clipboard.writeText(text);
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        },

        async testLocalPdf(event) {
            const files = event.target.files;
            if (files.length === 0) return;

            const file = files[0];
            if (file.type !== 'application/pdf') {
                alert('Pilih file dokumen PDF.');
                return;
            }

            this.testingFile = true;
            this.testResult = null;

            const formData = new FormData();
            formData.append('pdf_file', file);

            try {
                const response = await window.axios.post('{{ route('verify.integrity') }}', formData, {
                    headers: { 'Accept': 'application/json' }
                });

                if (response.data && response.data.hash) {
                    this.testResult = (response.data.hash.toLowerCase() === this.targetHash.toLowerCase());
                } else {
                    this.testResult = false;
                }
            } catch (err) {
                this.testResult = false;
            } finally {
                this.testingFile = false;
            }
        }
    };
}
</script>
@endpush
