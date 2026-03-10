@extends('layouts.admin')

@section('title', 'Log Aktivitas Ibadah')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-6 animate-fadeIn">
        <div>
            <h1 class="text-3xl font-serif font-black text-teal-950 uppercase tracking-tight">Catatan Ibadah</h1>
            <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                Monitoring Sinkronisasi Ibadah Hunter
            </p>
        </div>
    
    <div class="flex flex-col md:flex-row items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            @php $currentLimit = request('limit', 15); @endphp
            <div class="flex gap-1">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-log px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-log px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-log px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
            </div>
        </div>

        <div class="px-6 py-4 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 font-bold shadow-sm min-w-[140px]">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Total Entri:</span>
            <span class="text-xl font-mono leading-none">{{ number_format($logs instanceof \Illuminate\Pagination\LengthAwarePaginator ? $logs->total() : $logs->count()) }}</span>
        </div>
    </div>
</div>

<div class="glass-panel p-0 rounded-[32px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
    <div class="overflow-x-auto p-6 pt-5">
        <table class="w-full text-left" id="log-table">
            <thead>
                <tr class="border-b border-slate-100 uppercase">
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Node Waktu</th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                        Identitas Hunter <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Protokol Ritual</th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group text-center" onclick="sortTable(3)">
                        Yield (EXP) <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">Verifikasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 font-sans" id="log-body">
                @forelse($logs as $log)
                <tr class="log-row group hover:bg-slate-50/50 transition-colors">
                    <td class="py-6 px-4">
                        <div class="flex flex-col">
                            <span class="text-xs font-black text-teal-900 font-mono tracking-tighter">{{ $log->created_at->format('H:i:s') }}</span>
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-0.5">{{ $log->created_at->format('Y.m.d') }}</span>
                        </div>
                    </td>
                    <td class="py-6 px-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-teal-900 flex items-center justify-center font-serif font-black text-white text-sm shadow-lg group-hover:scale-110 transition-all duration-500 border border-teal-800">
                                {{ substr($log->user->username ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none">
                                    {{ $log->user->username ?? 'NODE HILANG' }}
                                </h3>
                                <code class="text-[9px] font-black text-slate-300 lowercase mt-1.5 block opacity-70">{{ $log->user->email ?? 'unknown@matrix' }}</code>
                            </div>
                        </div>
                    </td>
                    <td class="py-6 px-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 shadow-inner group-hover:text-cyan-500 group-hover:border-cyan-100 transition-all">
                                <i class="fas {{ $log->prayer->icon ?? 'fa-pray' }} text-[11px]"></i>
                            </div>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-[0.1em]">{{ $log->prayer->name ?? 'RITUAL TIDAK DIKENAL' }}</span>
                        </div>
                    </td>
                    <td class="py-6 px-4 text-center">
                         <div class="inline-flex items-center px-4 py-2 rounded-2xl bg-white border-2 border-slate-100 text-blue-600 shadow-sm group-hover:border-blue-200 transition-colors">
                            <span class="text-base font-black font-mono tracking-tighter">+{{ number_format($log->points_earned) }}</span>
                            <span class="text-[8px] font-black ml-1.5 uppercase opacity-60">EXP</span>
                         </div>
                    </td>
                    <td class="py-6 px-4 text-right whitespace-nowrap">
                         <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 border-2 border-emerald-100 text-[9px] font-black text-emerald-600 uppercase tracking-[0.15em] shadow-sm">
                            <i class="fas fa-check-double text-[9px] animate-pulse"></i>
                            Tervalidasi
                         </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-24 text-center">
                         <div class="flex flex-col items-center opacity-40">
                            <div class="w-20 h-20 rounded-full border-2 border-dashed border-slate-300 flex items-center justify-center mb-6">
                                <i class="fas fa-clock-rotate-left text-3xl text-slate-300"></i>
                            </div>
                            <p class="text-teal-900 font-serif font-black text-xl italic uppercase tracking-widest">Data Ritual Kosong</p>
                            <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-[0.5em]">Log sistem bersih dari sinkronisasi spiritual terbaru</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    <div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
        <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
            Menampilkan Entri Riwayat Ibadah Hunter
        </div>
        
        <div class="flex items-center gap-4">
            @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                @if($logs->onFirstPage())
                    <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm active:scale-95 font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </a>
                @endif

                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                    <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">{{ $logs->currentPage() }}</span>
                    <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $logs->lastPage() }}</span>
                </div>

                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
                        Selanjutnya <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                    </a>
                @else
                    <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                        Selanjutnya <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                    </span>
                @endif
            @else
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                    <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">1</span>
                    <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">SEMUA DATA TERMUAT</span>
                </div>
            @endif
        </div>
    </div>

<script>
    let currentLimit = 16;
    let sortOrders = { 1: 'asc', 3: 'asc' };

    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function applyDisplay() {
        // Obsolete: Handled server-side
    }
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
    }

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('log-body');
        const rows = Array.from(document.querySelectorAll('.log-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            // Numerical sorting for EXP reward at column 3
            if (columnIndex === 3) {
                valA = parseInt(valA.replace(/[^0-9]/g, '')) || 0;
                valB = parseInt(valB.replace(/[^0-9]/g, '')) || 0;
            }
            
            if (valA < valB) return isAsc ? -1 : 1;
            if (valA > valB) return isAsc ? 1 : -1;
            return 0;
        });

        sortOrders[columnIndex] = isAsc ? 'desc' : 'asc';
        rows.forEach(row => tableBody.appendChild(row));
        applyDisplay();
    }

    window.onload = () => applyDisplay();
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)); }
</style>
@endsection
