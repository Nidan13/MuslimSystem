@extends('layouts.admin')

@section('title', 'Registri Hunter')

@section('content')
<div class="space-y-10">
    {{-- Header Area --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 animate-fadeIn">
        <div>
            <h2 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Registri Hunter</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
                Node Identifikasi Hunter Terotorisasi Sistem
            </p>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            {{-- Row Limit Controls --}}
            <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
                @php $currentLimit = request('limit', 16); @endphp
                <div class="flex gap-1" id="row-limit-container">
                    <button type="button" onclick="setRowLimit(8)" class="row-btn-hunter px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                    <button type="button" onclick="setRowLimit(16)" class="row-btn-hunter px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                    <button type="button" onclick="setRowLimit('all')" class="row-btn-hunter px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
                </div>
            </div>

            {{-- Add Button --}}
            <a href="{{ route('admin.hunters.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
                <span class="relative flex items-center gap-3 tracking-[0.1em] text-sm font-serif uppercase">
                    <i class="fas fa-plus-circle text-cyan-400 icon-glow transition-transform group-hover:rotate-90"></i>
                    Daftarkan Hunter
                </span>
            </a>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="glass-panel p-0 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl shadow-slate-200/50">
        <div class="overflow-x-auto p-6 pt-5">
            <table class="w-full text-left" id="hunter-table">
                <thead>
                    <tr class="border-b-2 border-slate-100 uppercase">
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em]">Node Identitas</th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                            <div class="flex items-center justify-center gap-2">
                                OTORISASI <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(2)">
                            <div class="flex items-center gap-2">
                                KEMAMPUAN <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(3)">
                            <div class="flex items-center justify-end gap-2">
                                VAULT SP <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">SINKRONISASI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-sans" id="hunter-table-body">
                    @foreach($users as $user)
                    <tr class="hunter-row group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="py-6 px-4">
                            <div class="flex items-center gap-5">
                                <div class="relative">
                                    <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center font-serif font-black text-white text-xl shadow-lg shadow-teal-950/20 group-hover:scale-110 transition-transform duration-500">
                                        {{ substr($user->username, 0, 1) }}
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-cyan-400 border-[3px] border-white shadow-sm"></div>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none mb-1.5">
                                        {{ $user->username }}
                                    </h3>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                        <span class="text-cyan-500 font-mono">SN-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                        <span class="lowercase tracking-normal text-slate-300 font-medium">{{ $user->email }}</span>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-6 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="text-3xl font-serif font-black {{ $user->rankTier->color_code ? '' : 'text-teal-900' }} italic leading-none" style="{{ $user->rankTier->color_code ? 'color: ' . $user->rankTier->color_code : '' }}">
                                    {{ $user->rankTier->slug ?? 'E' }}
                                </span>
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-3 py-1.5 rounded-xl mt-3 border border-slate-100 shadow-inner">
                                    {{ $user->job_class ?? 'Initiate' }}
                                </span>
                            </div>
                        </td>
                        <td class="py-6 px-6">
                            <div class="w-48 space-y-3">
                                <div class="flex justify-between items-end">
                                    <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Level {{ $user->level }}</span>
                                    <span class="text-[9px] font-black text-cyan-500">{{ number_format(($user->current_exp / ($user->next_level_exp ?: 1000)) * 100) }}%</span>
                                </div>
                                <div class="h-2 bg-slate-50 rounded-full overflow-hidden border border-slate-100 shadow-inner">
                                    @php $expProgress = min(($user->current_exp / ($user->next_level_exp ?: 1000)) * 100, 100); @endphp
                                    <div class="h-full bg-gradient-to-r from-teal-900 to-cyan-400 transition-all duration-1000 rounded-full" style="width: {{ $expProgress }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-6 text-right">
                            <div class="inline-flex flex-col items-end">
                                <p class="text-xl font-serif font-black text-teal-900 tracking-tighter leading-none mb-2">
                                    {{ number_format($user->current_exp) }}
                                </p>
                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest">EXP</span>
                            </div>
                        </td>
                        <td class="py-6 text-right px-4">
                            <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-4 group-hover:translate-x-0">
                                <a href="{{ route('admin.hunters.edit', $user) }}" class="w-11 h-11 rounded-2xl bg-white border-2 border-slate-100 flex items-center justify-center text-teal-900 hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm active:scale-95">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <a href="{{ route('admin.hunters.show', $user) }}" class="w-11 h-11 rounded-2xl bg-teal-900 text-cyan-400 flex items-center justify-center hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20 active:scale-95">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer Area / Navigation --}}
    <div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
        <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
            Total Populasi Hunter: {{ $users instanceof \Illuminate\Pagination\LengthAwarePaginator ? $users->total() : $users->count() }}
        </div>
        
        <div class="flex items-center gap-4">
            @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                @if($users->onFirstPage())
                    <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-xl shadow-slate-200/50 active:scale-95 font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </a>
                @endif

                <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                    <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-xl shadow-sm border border-slate-100 leading-none flex items-center justify-center min-w-[2.5rem]">
                        {{ $users->currentPage() }}
                    </span>
                    <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $users->lastPage() }}</span>
                </div>

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
                        Selanjutnya <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                    </a>
                @else
                    <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                        Selanjutnya <i class="fas fa-chevron-right ml-2 text-[8px]"></i>
                    </span>
                @endif
            @else
                <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                    <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-xl shadow-sm border border-slate-100 leading-none flex items-center justify-center min-w-[2.5rem]">1</span>
                    <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">SEMUA DATA TERMUAT</span>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function setRowLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 1); // Reset to page 1 when changing limit
        window.location.href = url.toString();
    }

    let sortOrders = { 1: 'asc', 2: 'asc', 3: 'asc' };

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('hunter-table-body');
        const rows = Array.from(document.querySelectorAll('.hunter-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            if (columnIndex === 2) { // Level/Progress
                valA = parseInt(valA.split('\n')[0].replace(/[^0-9]/g, '')) || 0;
                valB = parseInt(valB.split('\n')[0].replace(/[^0-9]/g, '')) || 0;
            } else if (columnIndex === 3) { // Soul Points
                valA = parseInt(valA.replace(/,/g, '')) || 0;
                valB = parseInt(valB.replace(/,/g, '')) || 0;
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
    .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.6)); }
    .glass-panel { backdrop-filter: blur(16px); }
</style>
@endsection
