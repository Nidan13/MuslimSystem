@extends('layouts.admin')

@section('title', 'Arsip Log Aktivitas Sistem')

@section('content')
<div class="space-y-10">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 animate-fadeIn">
        <div>
            <h2 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Log Sistem</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
                Pemantauan Interaksi & Jejak Aktivitas
            </p>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <!-- Row Limit Controls -->
            <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
                @php $currentLimit = request('limit', 16); @endphp
                <div class="flex gap-1" id="row-limit-container">
                    <button type="button" onclick="setRowLimit(8)" class="row-btn-log px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                    <button type="button" onclick="setRowLimit(16)" class="row-btn-log px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                    <button type="button" onclick="setRowLimit('all')" class="row-btn-log px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
                </div>
            </div>

            <!-- Filter (Search) -->
            <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Log Aktivitas..." 
                    class="pl-12 pr-6 py-4 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 font-bold placeholder-slate-300 outline-none focus:border-cyan-400 transition-all text-[10px] uppercase tracking-widest w-64 shadow-sm shadow-slate-200/50">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-cyan-400 transition-colors"></i>
            </form>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-panel p-0 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl shadow-slate-200/50">
        <div class="overflow-x-auto p-6 pt-5">
            <table class="w-full text-left" id="log-table">
                <thead>
                    <tr class="border-b-2 border-slate-100 uppercase">
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(0)">
                            <div class="flex items-center gap-2">
                                TANGGAL & WAKTU <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity" id="sort-icon-0"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                            <div class="flex items-center gap-2">
                                PELAKU (AKTOR) <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity" id="sort-icon-1"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(2)">
                            <div class="flex items-center gap-2">
                                JENIS AKTIVITAS <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity" id="sort-icon-2"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">TARGET AKSI</th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">AKSI (TINDAKAN)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-sans" id="log-body">
                    @forelse($logs as $log)
                    <tr class="log-row group hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 px-4">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-teal-900 font-mono tracking-tighter">{{ $log->created_at->format('H:i:s') }}</span>
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">{{ $log->created_at->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="py-6 px-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-teal-900 border-2 border-teal-800 flex items-center justify-center font-serif font-black text-white text-xs shadow-md group-hover:scale-105 transition-transform">
                                    {{ substr($log->user->username ?? 'SYS', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest leading-none">Eksekutor</span>
                                    <span class="text-sm font-black text-teal-900 uppercase tracking-tight mt-1">{{ $log->user->username ?? 'Sistem Utama' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-6">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-xl border-2 {{ str_contains($log->description, 'delete') ? 'bg-red-50 text-red-600 border-red-100' : (str_contains($log->description, 'update') ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-cyan-50 text-cyan-700 border-cyan-100') }} text-[9px] font-black uppercase tracking-widest shadow-sm">
                                {{ $log->description }}
                            </span>
                        </td>
                        <td class="py-6 px-6">
                             <div class="flex flex-col">
                                <span class="text-sm font-serif font-black text-teal-900 uppercase tracking-tight">{{ str_replace('_', ' ', strtoupper($log->type)) }}</span>
                                <span class="text-[9px] font-mono font-black text-slate-300 uppercase tracking-tighter mt-0.5">JUMLAH: {{ $log->amount ?? '-' }}</span>
                             </div>
                        </td>
                        <td class="py-6 px-6 text-right whitespace-nowrap">
                             <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">

                                 <form action="{{ route('admin.activity-logs.destroy', $log) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this, 'Log #{{ $log->id }}')" class="p-4 bg-white border-2 border-slate-100 text-slate-400 hover:text-red-500 hover:border-red-200 rounded-2xl transition-all shadow-sm active:scale-95 leading-none shadow-slate-200/50">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                             </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-24 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <i class="fas fa-clipboard-list text-6xl text-slate-100 mb-6"></i>
                                <span class="text-slate-300 text-[10px] font-black uppercase tracking-[0.4em]">Tidak ada data log aktivitas</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer Area / Navigation -->
    <div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
        <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
            Daftar Log Aktivitas Sistem (Total Data: {{ $logs instanceof \Illuminate\Pagination\LengthAwarePaginator ? $logs->total() : $logs->count() }})
        </div>
        
        <div class="flex items-center gap-4">
            @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <!-- Navigation -->
                @if($logs->onFirstPage())
                    <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-xl shadow-slate-200/50 active:scale-95 font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </a>
                @endif

                <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                    <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-xl shadow-sm border border-slate-100 leading-none flex items-center justify-center min-w-[2.5rem]">
                        {{ $logs->currentPage() }}
                    </span>
                    <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $logs->lastPage() }}</span>
                </div>

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
                        Selanjutnya <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                    </a>
                @else
                    <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                        Selanjutnya <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                    </span>
                @endif
            @else
                <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                    <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-xl shadow-sm border border-slate-100 leading-none flex items-center justify-center min-w-[2.5rem]">
                        1
                    </span>
                    <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">SEMUA DATA TERMUAT</span>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let currentLimit = 16;
        let sortOrders = { 0: 'asc', 1: 'asc', 2: 'asc' };

        window.setRowLimit = function(limit) {
            currentLimit = limit;
            
            document.querySelectorAll('.row-btn-log').forEach(btn => {
                const label = limit === 'all' ? 'Semua' : limit.toString();
                if (btn.innerText.trim() === label) {
                    btn.className = 'row-btn-log px-4 py-2 rounded-xl text-[10px] font-black transition-all bg-teal-900 text-white shadow-lg';
                } else {
                    btn.className = 'row-btn-log px-4 py-2 rounded-xl text-[10px] font-black transition-all hover:bg-white hover:text-cyan-500 text-slate-400';
                }
            });

            applyDisplay();
        };

        window.applyDisplay = function() {
            const rows = document.querySelectorAll('.log-row');
            let visibleCount = 0;

            rows.forEach(row => {
                if (currentLimit === 'all' || visibleCount < currentLimit) {
                    row.style.display = 'table-row';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
        };

        window.sortTable = function(columnIndex) {
            const tableBody = document.getElementById('log-body');
            const rows = Array.from(document.querySelectorAll('.log-row'));
            const isAsc = sortOrders[columnIndex] === 'asc';

            // Reset and Update Icons
            document.querySelectorAll('th i').forEach(icon => {
                if (icon) {
                    icon.className = 'fas fa-sort opacity-20 transition-opacity';
                    icon.style.color = 'inherit';
                }
            });
            const activeIcon = document.getElementById(`sort-icon-${columnIndex}`);
            if (activeIcon) {
                activeIcon.className = `fas fa-sort-${isAsc ? 'up' : 'down'} opacity-100`;
                activeIcon.style.color = '#22d3ee'; // cyan-400
            }

            rows.sort((a, b) => {
                let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
                let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
                
                if (valA < valB) return isAsc ? -1 : 1;
                if (valA > valB) return isAsc ? 1 : -1;
                return 0;
            });

            sortOrders[columnIndex] = isAsc ? 'desc' : 'asc';
            rows.forEach(row => tableBody.appendChild(row));
            applyDisplay();
        };

        // Initialize state
        applyDisplay();
        setRowLimit(16);
    });
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.6)); }
    .glass-panel { backdrop-filter: blur(16px); }
</style>
@endsection
