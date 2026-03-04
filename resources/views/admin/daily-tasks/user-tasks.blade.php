@extends('layouts.admin')

@section('title', 'Manifestasi Ritual: ' . $user->username)

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6 animate-fadeIn">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">{{ $user->username }} <span class="text-cyan-500 font-sans tracking-tight ml-2">Node</span></h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
            Inspeksi Protokol Harian Terlokalisasi
        </p>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            @php $currentLimit = request('limit', 16); @endphp
            <div class="flex gap-1" id="row-limit-container">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-user-task px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-user-task px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-user-task px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
            </div>
        </div>
        <a href="{{ route('admin.daily-tasks.users') }}" class="group relative px-6 py-4 rounded-2xl bg-white border-2 border-slate-100 text-teal-900 font-bold shadow-xl shadow-slate-200/50 hover:border-cyan-400 hover:text-cyan-600 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-xs font-serif uppercase">
                <i class="fas fa-arrow-left text-teal-500 transition-transform group-hover:-translate-x-1"></i>
                Registri Kustom
            </span>
        </a>
    </div>
</div>

<div class="glass-panel p-0 rounded-[32px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
    <div class="overflow-x-auto p-6 pt-5">
        <table class="w-full text-left" id="user-task-table">
            <thead>
                <tr class="border-b border-slate-100 uppercase">
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Inti</th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                        Nama Ritual <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Direktif</th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(3)">
                        Hasil SP <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center">Status</th>
                    <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">Tanggal Manifestasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 font-sans" id="user-task-body">
                @forelse($tasks as $task)
                <tr class="user-task-row group hover:bg-slate-50/50 transition-colors">
                    <td class="py-6 px-4">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 border-2 border-slate-50 flex items-center justify-center text-slate-400 shadow-inner group-hover:bg-white group-hover:border-cyan-100 transition-all duration-500">
                            @if(Str::startsWith($task->icon, 'fa') || Str::contains($task->icon, 'fa-'))
                                <i class="{{ $task->icon }} text-lg group-hover:text-cyan-500 transition-colors"></i>
                            @else
                                <span class="text-xl opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all">{{ $task->icon }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="py-6 px-4">
                        <span class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none">
                            {{ $task->name }}
                        </span>
                    </td>
                    <td class="py-6 px-4">
                        <p class="text-[11px] text-slate-500 font-medium max-w-xs leading-relaxed italic line-clamp-2">
                            {{ $task->description }}
                        </p>
                    </td>
                    <td class="py-6 px-4 text-right whitespace-nowrap">
                        <div class="inline-flex flex-col items-end">
                            <p class="text-lg font-black text-amber-600 font-mono tracking-tighter leading-none mb-1">
                                +{{ number_format($task->soul_points) }}
                            </p>
                            <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest leading-none">Soul Points</span>
                        </div>
                    </td>
                    <td class="py-6 px-4 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 {{ $task->is_active ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-slate-400 bg-slate-50 border-slate-200' }} text-[9px] font-black uppercase tracking-widest shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full {{ $task->is_active ? 'bg-emerald-400 shadow-[0_0_5px_rgba(52,211,153,0.5)]' : 'bg-slate-300' }}"></span>
                            {{ $task->is_active ? 'Aktif' : 'Nonaktif' }}
                        </div>
                    </td>
                    <td class="py-6 text-right px-4">
                         <span class="text-[10px] text-slate-400 font-mono font-bold tracking-tighter bg-slate-50 px-2 py-1 rounded shadow-sm border border-slate-100">
                             {{ $task->created_at->format('Y.m.d') }}
                         </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-20 text-center text-slate-300 italic font-medium uppercase tracking-[0.2em] text-[10px]">
                        Hunter ini belum memanifestasikan protokol personal...
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
    <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
        Total Ritual Hunter Terdeteksi: {{ $tasks instanceof \Illuminate\Pagination\LengthAwarePaginator ? $tasks->total() : $tasks->count() }}
    </div>
    
    <div class="flex items-center gap-4">
        @if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @if($tasks->onFirstPage())
                <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $tasks->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm active:scale-95 font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </a>
            @endif

            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">{{ $tasks->currentPage() }}</span>
                <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $tasks->lastPage() }}</span>
            </div>

            @if($tasks->hasMorePages())
                <a href="{{ $tasks->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
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
    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    let sortOrders = { 1: 'asc', 3: 'asc' };

    function applyDisplay() {
        // Obsolete
    }

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('user-task-body');
        const rows = Array.from(document.querySelectorAll('.user-task-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            // Handle numeric values for SP column
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

    window.onload = () => {};
</script>

<style>
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
