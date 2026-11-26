@extends('layouts.app')

@section('title', 'Log Aktivitas & Jejak Audit Kriptografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ selectedDetails: null }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <div class="text-xs font-mono font-bold text-blue-900 uppercase tracking-wider mb-0.5">Keamanan & Audit Sistem</div>
            <h1 class="text-2xl font-bold text-[#0F172A]">Log Aktivitas & Audit Trail Kriptografi</h1>
            <p class="text-xs text-slate-500 mt-1">
                Catatan komprehensif seluruh aktivitas pengajuan, penandatanganan, verifikasi scan QR, pengujian hash SHA-256, dan pencabutan surat.
            </p>
        </div>
    </div>

    <!-- Activity Counters -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 mb-6 text-xs">
        <a href="{{ route('audit-logs.index', ['action' => 'SUBMITTED']) }}" class="card-clean rounded p-3 text-center hover:bg-slate-50 transition {{ request('action') === 'SUBMITTED' ? 'ring-2 ring-blue-800' : '' }}">
            <div class="text-[10px] text-slate-500 font-semibold uppercase">Diajukan</div>
            <div class="font-mono font-bold text-slate-900 text-lg mt-0.5">{{ $actionCounts['SUBMITTED'] }}</div>
        </a>

        <a href="{{ route('audit-logs.index', ['action' => 'APPROVED']) }}" class="card-clean rounded p-3 text-center hover:bg-slate-50 transition {{ request('action') === 'APPROVED' ? 'ring-2 ring-emerald-600' : '' }}">
            <div class="text-[10px] text-emerald-700 font-semibold uppercase">Disahkan</div>
            <div class="font-mono font-bold text-emerald-900 text-lg mt-0.5">{{ $actionCounts['APPROVED'] }}</div>
        </a>

        <a href="{{ route('audit-logs.index', ['action' => 'REJECTED']) }}" class="card-clean rounded p-3 text-center hover:bg-slate-50 transition {{ request('action') === 'REJECTED' ? 'ring-2 ring-rose-600' : '' }}">
            <div class="text-[10px] text-rose-700 font-semibold uppercase">Ditolak</div>
            <div class="font-mono font-bold text-rose-900 text-lg mt-0.5">{{ $actionCounts['REJECTED'] }}</div>
        </a>

        <a href="{{ route('audit-logs.index', ['action' => 'REVOKED']) }}" class="card-clean rounded p-3 text-center hover:bg-slate-50 transition {{ request('action') === 'REVOKED' ? 'ring-2 ring-rose-600' : '' }}">
            <div class="text-[10px] text-rose-700 font-semibold uppercase">Dicabut</div>
            <div class="font-mono font-bold text-rose-900 text-lg mt-0.5">{{ $actionCounts['REVOKED'] }}</div>
        </a>

        <a href="{{ route('audit-logs.index', ['action' => 'VERIFIED_QR']) }}" class="card-clean rounded p-3 text-center hover:bg-slate-50 transition {{ request('action') === 'VERIFIED_QR' ? 'ring-2 ring-blue-800' : '' }}">
            <div class="text-[10px] text-blue-800 font-semibold uppercase">Scan QR</div>
            <div class="font-mono font-bold text-blue-950 text-lg mt-0.5">{{ $actionCounts['VERIFIED_QR'] }}</div>
        </a>

        <a href="{{ route('audit-logs.index', ['action' => 'VERIFIED_HASH_MATCH']) }}" class="card-clean rounded p-3 text-center hover:bg-slate-50 transition {{ request('action') === 'VERIFIED_HASH_MATCH' ? 'ring-2 ring-emerald-600' : '' }}">
            <div class="text-[10px] text-emerald-700 font-semibold uppercase">Hash Cocok</div>
            <div class="font-mono font-bold text-emerald-900 text-lg mt-0.5">{{ $actionCounts['VERIFIED_HASH_MATCH'] }}</div>
        </a>

        <a href="{{ route('audit-logs.index', ['action' => 'VERIFIED_HASH_MISMATCH']) }}" class="card-clean rounded p-3 text-center hover:bg-slate-50 transition {{ request('action') === 'VERIFIED_HASH_MISMATCH' ? 'ring-2 ring-rose-600' : '' }}">
            <div class="text-[10px] text-rose-700 font-semibold uppercase">Hash Beda</div>
            <div class="font-mono font-bold text-rose-900 text-lg mt-0.5">{{ $actionCounts['VERIFIED_HASH_MISMATCH'] }}</div>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card-clean rounded-lg p-4 mb-6">
        <form action="{{ route('audit-logs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 text-xs">
            <div class="flex-grow">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Cari nomor surat, nama user, detail hash, atau IP address..."
                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none"
                >
            </div>

            <div>
                <select name="action" class="w-full px-3 py-2 bg-white border border-slate-300 rounded focus:ring-2 focus:ring-[#1E3A8A] focus:outline-none">
                    <option value="">Semua Aksi</option>
                    <option value="SUBMITTED" {{ request('action') === 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED (Pengajuan)</option>
                    <option value="APPROVED" {{ request('action') === 'APPROVED' ? 'selected' : '' }}>APPROVED (Pengesahan)</option>
                    <option value="REJECTED" {{ request('action') === 'REJECTED' ? 'selected' : '' }}>REJECTED (Penolakan)</option>
                    <option value="REVOKED" {{ request('action') === 'REVOKED' ? 'selected' : '' }}>REVOKED (Pencabutan)</option>
                    <option value="DOWNLOADED" {{ request('action') === 'DOWNLOADED' ? 'selected' : '' }}>DOWNLOADED (Unduh PDF)</option>
                    <option value="VERIFIED_QR" {{ request('action') === 'VERIFIED_QR' ? 'selected' : '' }}>VERIFIED_QR (Scan QR)</option>
                    <option value="VERIFIED_HASH_MATCH" {{ request('action') === 'VERIFIED_HASH_MATCH' ? 'selected' : '' }}>VERIFIED_HASH_MATCH (Integritas Asli)</option>
                    <option value="VERIFIED_HASH_MISMATCH" {{ request('action') === 'VERIFIED_HASH_MISMATCH' ? 'selected' : '' }}>VERIFIED_HASH_MISMATCH (Dimodifikasi)</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="px-5 py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded font-semibold transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'action']))
                    <a href="{{ route('audit-logs.index') }}" class="p-2 text-slate-500 hover:text-slate-800">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="card-clean rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs text-left divide-y divide-slate-200">
                <thead class="bg-slate-50 font-semibold text-slate-700">
                    <tr>
                        <th class="py-3 px-4">Waktu (WIB)</th>
                        <th class="py-3 px-4">Aksi Audit</th>
                        <th class="py-3 px-4">Terkait Surat</th>
                        <th class="py-3 px-4">Pelaksana / User</th>
                        <th class="py-3 px-4">IP Address</th>
                        <th class="py-3 px-4 text-right">Rincian JSON</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-2.5 px-4 font-mono text-[11px] text-slate-600 whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>

                            <td class="py-2.5 px-4">
                                @if(in_array($log->action, ['APPROVED', 'VERIFIED_HASH_MATCH']))
                                    <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-[10px] badge-emerald">
                                        {{ $log->action }}
                                    </span>
                                @elseif(in_array($log->action, ['REJECTED', 'REVOKED', 'VERIFIED_HASH_MISMATCH']))
                                    <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-[10px] badge-crimson">
                                        {{ $log->action }}
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded font-mono font-bold text-[10px] bg-slate-100 text-slate-800 border border-slate-300">
                                        {{ $log->action }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-2.5 px-4">
                                @if($log->letter)
                                    <a href="{{ route('letters.show', $log->letter) }}" class="font-mono font-bold text-blue-900 hover:underline">
                                        {{ $log->letter->letter_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <td class="py-2.5 px-4">
                                @if($log->user)
                                    <span class="font-semibold text-slate-900">{{ $log->user->name }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $log->user->jabatan }}</span>
                                @else
                                    <span class="text-slate-500 italic">Publik / Anonim</span>
                                @endif
                            </td>

                            <td class="py-2.5 px-4 font-mono text-[11px] text-slate-600">
                                {{ $log->ip_address ?: '-' }}
                            </td>

                            <td class="py-2.5 px-4 text-right">
                                @if($log->details)
                                    <button
                                        type="button"
                                        @click="selectedDetails = {{ json_encode($log->details) }}"
                                        class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded font-mono text-[10px] font-semibold transition cursor-pointer"
                                    >
                                        Lihat JSON
                                    </button>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada catatan audit log yang cocok dengan filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-200">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- JSON DETAIL MODAL -->
    <div x-show="selectedDetails !== null" class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="card-clean rounded-lg max-w-lg w-full p-6 shadow-xl" @click.away="selectedDetails = null">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-bold text-[#0F172A] font-mono">Detail Metadata Audit (JSON)</h3>
                <button type="button" @click="selectedDetails = null" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <pre class="bg-slate-900 text-emerald-400 p-4 rounded text-xs font-mono overflow-x-auto max-h-80 select-all" x-text="JSON.stringify(JSON.parse(selectedDetails || '{}'), null, 2)"></pre>

            <div class="mt-4 flex justify-end">
                <button type="button" @click="selectedDetails = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded text-xs font-semibold">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
