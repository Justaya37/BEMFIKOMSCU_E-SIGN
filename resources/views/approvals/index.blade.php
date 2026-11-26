@extends('layouts.app')

@section('title', 'Antrean Pengesahan Surat')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="text-xs font-mono font-bold text-amber-700 uppercase tracking-wider mb-0.5">Otoritas Ketua / Wakil Ketua</div>
            <h1 class="text-2xl font-bold text-[#0F172A]">Antrean Pengesahan Surat Masuk</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar draf surat resmi yang menunggu verifikasi dan pembubuhan tanda tangan digital Anda.</p>
        </div>

        <div class="flex items-center space-x-3">
            <span class="bg-amber-100 text-amber-900 border border-amber-300 text-xs font-bold font-mono px-3 py-1.5 rounded">
                {{ $pendingCount }} Surat Menunggu
            </span>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card-clean rounded-lg p-4 mb-6">
        <form action="{{ route('approvals.index') }}" method="GET" class="flex gap-3">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nomor surat, perihal, atau kegiatan..."
                class="flex-grow px-3 py-2 bg-white border border-slate-300 rounded text-xs focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
            >
            <button type="submit" class="px-5 py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded text-xs font-semibold transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('approvals.index') }}" class="p-2 text-xs text-slate-500 hover:text-slate-800">Reset</a>
            @endif
        </form>
    </div>

    <!-- Queue Table (Desktop) & Cards (Mobile) -->
    <div class="card-clean rounded-lg overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                <thead class="bg-slate-50 font-semibold text-slate-700">
                    <tr>
                        <th class="py-3 px-4">Nomor & Tanggal Surat</th>
                        <th class="py-3 px-4">Perihal & Dasar Kegiatan</th>
                        <th class="py-3 px-4">Jumlah Mahasiswa</th>
                        <th class="py-3 px-4">Pengusul (Sekretaris)</th>
                        <th class="py-3 px-4 text-center">Waktu Menunggu</th>
                        <th class="py-3 px-4 text-right">Aksi Review</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($letters as $letter)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4">
                                <div class="font-mono font-bold text-slate-900">{{ $letter->letter_number }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5">{{ $letter->letter_date->translatedFormat('d F Y') }}</div>
                                <span class="inline-block mt-1 bg-blue-50 text-blue-800 text-[10px] px-1.5 py-0.2 rounded font-semibold border border-blue-200">
                                    {{ $letter->category_label }}
                                </span>
                            </td>

                            <td class="py-3 px-4 max-w-sm">
                                <div class="font-semibold text-slate-900 truncate" title="{{ $letter->title }}">{{ $letter->title }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5 truncate" title="{{ $letter->event_name }}">{{ $letter->event_name }}</div>
                            </td>

                            <td class="py-3 px-4">
                                <span class="font-mono font-semibold text-slate-800">{{ $letter->recipients->count() }} Orang</span>
                            </td>

                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-800">{{ $letter->submitter?->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $letter->submitter?->jabatan }}</div>
                            </td>

                            <td class="py-3 px-4 text-center font-mono text-[11px] text-slate-500">
                                {{ $letter->created_at->diffForHumans() }}
                            </td>

                            <td class="py-3 px-4 text-right">
                                <a
                                    href="{{ route('approvals.show', $letter) }}"
                                    class="inline-flex items-center space-x-1.5 px-3.5 py-1.5 rounded bg-emerald-700 hover:bg-emerald-800 text-white font-semibold transition shadow-sm"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Tinjau & Sahkan</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="font-semibold text-slate-600">Semua Antrean Bersih!</div>
                                <div class="text-xs text-slate-400 mt-0.5">Tidak ada draf surat resmi yang menunggu persetujuan Anda saat ini.</div>
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
                            <div class="font-mono font-bold text-sm text-[#0F172A]">{{ $letter->letter_number }}</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">{{ $letter->letter_date->translatedFormat('d F Y') }}</div>
                        </div>
                        <span class="bg-blue-50 text-blue-800 text-[10px] px-2 py-0.5 rounded font-semibold border border-blue-200 shrink-0">
                            {{ $letter->category_label }}
                        </span>
                    </div>

                    <div>
                        <div class="font-bold text-xs text-slate-900 leading-snug">{{ $letter->title }}</div>
                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $letter->event_name }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-2.5 bg-slate-50 rounded-lg text-[11px] text-slate-600">
                        <div>
                            <span class="text-slate-400 text-[10px] block font-medium">Pengusul:</span>
                            <span class="font-semibold text-slate-800">{{ $letter->submitter?->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block font-medium">Waktu Menunggu:</span>
                            <span class="font-mono text-amber-700 font-semibold">{{ $letter->created_at->diffForHumans() }}</span>
                        </div>
                        @if($letter->recipients->count() > 0)
                            <div class="col-span-2 pt-1 border-t border-slate-200/60">
                                <span class="text-slate-400 text-[10px]">Penerima:</span>
                                <span class="font-semibold text-slate-800">{{ $letter->recipients->count() }} Mahasiswa Terlampir</span>
                            </div>
                        @endif
                    </div>

                    <a
                        href="{{ route('approvals.show', $letter) }}"
                        class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-sm transition min-h-[44px]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>Tinjau & Sahkan Surat</span>
                    </a>
                </div>
            @empty
                <div class="py-12 px-4 text-center text-slate-400">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="font-semibold text-slate-600">Semua Antrean Bersih!</div>
                    <div class="text-xs text-slate-400 mt-0.5">Tidak ada draf surat resmi yang menunggu persetujuan Anda saat ini.</div>
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
@endsection
