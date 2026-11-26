@extends('layouts.app')

@section('title', 'Detail Surat: ' . $letter->letter_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ copySuccess: false }">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('dashboard') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium">&larr; Kembali ke Dashboard</a>
                <span class="text-slate-300">&bull;</span>
                <span class="text-xs font-mono text-blue-900 font-semibold">{{ $letter->category_label }}</span>
            </div>
            <h1 class="text-2xl font-bold text-[#0F172A] mt-1 flex items-center space-x-3">
                <span>{{ $letter->letter_number }}</span>
                @if($letter->isApproved())
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold badge-emerald">Sah & Disahkan</span>
                @elseif($letter->isPending())
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold badge-amber">Menunggu Pengesahan</span>
                @elseif($letter->isRevoked())
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold badge-crimson">Pengesahan Dicabut</span>
                @else
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold badge-crimson">Ditolak</span>
                @endif
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($letter->isApproved())
                <a
                    href="{{ route('letters.download', $letter) }}"
                    class="inline-flex items-center space-x-1.5 px-4 py-2 rounded bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-semibold shadow-sm transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh PDF Resmi</span>
                </a>

                <a
                    href="{{ route('verify.show', $letter->verification_token) }}"
                    target="_blank"
                    class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold border border-slate-300 transition"
                >
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>Buka Halaman Verifikasi</span>
                </a>
            @else
                <a
                    href="{{ route('letters.download', $letter) }}"
                    class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold border border-slate-300 transition"
                >
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Unduh Draf PDF</span>
                </a>
            @endif

            @if(auth()->user()->canSign() && $letter->isPending())
                <a
                    href="{{ route('approvals.show', $letter) }}"
                    class="inline-flex items-center space-x-1.5 px-4 py-2 rounded bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold shadow-sm transition"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <span>Review & Sahkan Surat</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Rejection / Revocation Reason Box -->
    @if(($letter->isRejected() || $letter->isRevoked()) && $letter->rejection_or_revoke_reason)
        <div class="mb-6 card-clean rounded-lg p-4 bg-rose-50 border-rose-300 text-rose-900 text-xs">
            <div class="font-bold uppercase tracking-wider mb-1 flex items-center space-x-1.5">
                <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Catatan Resmi {{ $letter->isRevoked() ? 'Pencabutan' : 'Penolakan' }}</span>
            </div>
            <p>{{ $letter->rejection_or_revoke_reason }}</p>
        </div>
    @endif

    <!-- Main Content Split -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- LEFT: EMBEDDED PDF PREVIEW (7 Cols) -->
        <div class="lg:col-span-7 card-clean rounded-lg overflow-hidden flex flex-col h-[480px] sm:h-[600px] lg:h-[750px]">
            <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between text-xs">
                <div class="font-semibold text-slate-800 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Pratinjau Dokumen ({{ $letter->isApproved() ? 'PDF Resmi Bertanda Tangan' : 'Draf PDF Asli' }})</span>
                </div>
                <span class="text-[11px] font-mono text-slate-500">Iframe Viewer</span>
            </div>
            <div class="flex-grow bg-slate-200">
                <iframe
                    src="{{ route('letters.stream', ['letter' => $letter, 'type' => $letter->isApproved() ? 'signed' : 'draft']) }}"
                    class="w-full h-full border-0"
                    title="Pratinjau Dokumen"
                ></iframe>
            </div>
        </div>

        <!-- RIGHT: METADATA & RECIPIENTS (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <!-- Metadata Card -->
            <div class="card-clean rounded-lg p-5 text-xs space-y-3">
                <h3 class="font-bold text-[#0F172A] border-b border-slate-100 pb-2">Rincian Surat</h3>

                <div>
                    <span class="text-slate-500 block">Perihal Surat:</span>
                    <span class="font-semibold text-slate-900 block mt-0.5">{{ $letter->title }}</span>
                </div>

                <div>
                    <span class="text-slate-500 block">Nama Kegiatan:</span>
                    <span class="text-slate-800 block mt-0.5">{{ $letter->event_name }}</span>
                </div>

                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-slate-500 block">Tanggal Surat:</span>
                        <span class="font-semibold text-slate-800">{{ $letter->letter_date->translatedFormat('d F Y') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Diajukan Oleh:</span>
                        <span class="font-semibold text-slate-800">{{ $letter->submitter?->name }}</span>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-slate-500 block">Tata Letak Cap:</span>
                        <span class="font-mono text-slate-800 text-[11px]">X: {{ $letter->cap_x ?? 28 }} mm, Y: {{ $letter->cap_y ?? 195 }} mm</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Posisi QR Footer:</span>
                        <span class="font-mono text-slate-800 text-[11px] capitalize">{{ str_replace('_', ' ', $letter->qr_position ?: 'bottom_left') }}</span>
                    </div>
                </div>

                @if($letter->approved_by && $letter->approved_at)
                    <div class="pt-2 border-t border-slate-100 grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-slate-500 block">Disahkan Oleh:</span>
                            <span class="font-bold text-blue-900">{{ $letter->approver?->name }}</span>
                            <span class="text-[10px] text-slate-500 block">{{ $letter->approver?->jabatan }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block">Waktu Pengesahan:</span>
                            <span class="font-mono text-slate-800 font-semibold">{{ $letter->approved_at->format('d/m/Y H:i') }} WIB</span>
                        </div>
                    </div>
                @endif

                @if($letter->file_hash)
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-slate-500 block mb-1">SHA-256 Hash Dokumen:</span>
                        <div class="bg-slate-50 p-2 rounded border border-slate-200 font-mono text-[10px] text-slate-700 break-all select-all">
                            {{ $letter->file_hash }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recipients Card -->
            <div class="card-clean rounded-lg p-5 text-xs">
                <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-100">
                    <h3 class="font-bold text-[#0F172A]">Daftar Mahasiswa Resmi ({{ $letter->recipients->count() }})</h3>
                    <span class="text-[10px] text-slate-400 font-mono">Tervalidasi Database</span>
                </div>

                <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                    @forelse($letter->recipients as $idx => $recipient)
                        <div class="p-2.5 rounded bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $recipient->student_name }}</div>
                                <div class="font-mono text-[11px] text-slate-500">NIM: {{ $recipient->nim }}</div>
                            </div>
                            @if($recipient->notes)
                                <span class="text-[10px] bg-slate-200 text-slate-700 px-2 py-0.5 rounded">
                                    {{ $recipient->notes }}
                                </span>
                            @endif
                        </div>
                    @empty
                        <div class="py-4 text-center text-slate-400">Tidak ada daftar mahasiswa terlampir.</div>
                    @endforelse
                </div>
            </div>

            <!-- Audit Trail Log for this letter -->
            <div class="card-clean rounded-lg p-5 text-xs">
                <h3 class="font-bold text-[#0F172A] pb-2 mb-3 border-b border-slate-100">Riwayat Jejak Audit (Audit Trail)</h3>
                <div class="space-y-3">
                    @foreach($letter->auditLogs as $log)
                        <div class="flex items-start space-x-2 text-[11px]">
                            <div class="w-2 h-2 rounded-full bg-slate-400 mt-1.5 shrink-0"></div>
                            <div class="flex-grow">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono font-bold text-slate-800">{{ $log->action }}</span>
                                    <span class="text-slate-400 font-mono text-[10px]">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="text-slate-600">
                                    {{ $log->user ? $log->user->name : 'Publik / Sistem' }} (IP: {{ $log->ip_address ?: 'Unknown' }})
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
