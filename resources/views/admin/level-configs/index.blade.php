@extends('layouts.admin')

@section('title', 'Arsip Skala Kekuatan')

@section('content')
<div class="space-y-10">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 animate-fadeIn">
        <div>
            <h2 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Konfigurasi Level</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
                Pengaturan Level & Kebutuhan XP
            </p>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <!-- Row Limit Controls -->
            <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
                @php $currentLimit = request('limit', 16); @endphp
                <div class="flex gap-1" id="row-limit-container">
                    <button type="button" onclick="setRowLimit(8)" class="row-btn-level px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                    <button type="button" onclick="setRowLimit(16)" class="row-btn-level px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                    <button type="button" onclick="setRowLimit('all')" class="row-btn-level px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
                </div>
            </div>

            <!-- Add Button -->
            <a href="{{ route('admin.level-configs.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden font-serif uppercase tracking-widest text-[10px] font-black">
                <span class="relative flex items-center gap-3">
                    <i class="fas fa-plus text-cyan-400 icon-glow transition-transform group-hover:rotate-90"></i>
                    Tambah Level Baru
                </span>
            </a>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-panel p-0 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl shadow-slate-200/50">
        <div class="overflow-x-auto p-6 pt-5">
            <table class="w-full text-left" id="level-table">
                <thead>
                    <tr class="border-b-2 border-slate-100 uppercase">
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(0)">
                            <div class="flex items-center gap-2">
                                LEVEL PENGGUNA <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity" id="sort-icon-0"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                            <div class="flex items-center gap-2">
                                XP DIBUTUHKAN <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity" id="sort-icon-1"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">TOTAL XP KUMULATIF</th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center">POIN ATRIBUT</th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-sans" id="level-body">
                    @forelse($configs as $config)
                    <tr class="level-row group hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 px-6">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 rounded-2xl bg-teal-900 border-2 border-teal-800 flex items-center justify-center font-mono font-black text-white text-xl shadow-lg group-hover:scale-110 group-hover:shadow-cyan-400/20 transition-all">
                                    {{ $config->level }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest leading-none">Level Pengguna</span>
                                    <span class="text-sm font-serif font-black text-teal-900 uppercase tracking-tight mt-1">Level {{ $config->level }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-6 px-6">
                             <div class="inline-flex items-center gap-3 px-4 py-2 bg-white border-2 border-slate-100 rounded-2xl shadow-sm group-hover:border-cyan-100 transition-colors">
                                <i class="fas fa-bolt text-[10px] text-cyan-500"></i>
                                <span class="text-lg font-black text-teal-900 font-mono tracking-tighter">{{ number_format($config->xp_required) }}</span>
                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest">XP DIBUTUHKAN</span>
                            </div>
                        </td>
                        <td class="py-6 px-6">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-teal-900/60 font-mono tracking-tighter">
                                    Σ {{ number_format($config->xp_total_cumulative ?? 0) }}
                                </span>
                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest mt-0.5">Total XP Terkumpul</span>
                            </div>
                        </td>
                        <td class="py-6 px-6 text-center">
                            <div class="inline-flex items-center px-4 py-1.5 rounded-xl border-2 border-emerald-100 bg-emerald-50 text-[9px] font-black text-emerald-700 uppercase tracking-widest shadow-sm">
                                +{{ $config->stat_points_reward ?? 5 }} ATRIBUT (AP)
                            </div>
                        </td>
                        <td class="py-6 px-6 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                                <a href="{{ route('admin.level-configs.edit', $config) }}" class="p-4 bg-white border-2 border-slate-100 text-teal-900 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm active:scale-95 leading-none shadow-slate-200/50">
                                    <i class="fas fa-sliders text-xs"></i>
                                </a>
                                <form action="{{ route('admin.level-configs.destroy', $config) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this, 'Level {{ $config->level }}')" class="p-4 bg-white border-2 border-slate-100 text-slate-400 hover:text-red-500 hover:border-red-200 rounded-2xl transition-all shadow-sm active:scale-95 leading-none shadow-slate-200/50">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-layer-group text-slate-100 text-6xl mb-4"></i>
                                <span class="text-slate-300 text-[10px] font-black uppercase tracking-[0.3em]">Data Konfigurasi Level Masih Kosong</span>
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
            Data Konfigurasi Level (Total Data: {{ $configs instanceof \Illuminate\Pagination\LengthAwarePaginator ? $configs->total() : $configs->count() }})
        </div>
        
        <div class="flex items-center gap-4">
            @if($configs instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <!-- Navigation -->
                @if($configs->onFirstPage())
                    <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </span>
                @else
                    <a href="{{ $configs->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-xl shadow-slate-200/50 active:scale-95 font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </a>
                @endif

                <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                    <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-xl shadow-sm border border-slate-100 leading-none flex items-center justify-center min-w-[2.5rem]">
                        {{ $configs->currentPage() }}
                    </span>
                    <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $configs->lastPage() }}</span>
                </div>

                @if($configs->hasMorePages())
                    <a href="{{ $configs->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
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
        window.setRowLimit = function(limit) {
            const url = new URL(window.location.href);
            url.searchParams.set('limit', limit);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        };

        window.applyDisplay = function() {
            // Obsolete: Handled server-side
        };

        window.applyDisplay = function() {
            const rows = document.querySelectorAll('.level-row');
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
            const tableBody = document.getElementById('level-body');
            const rows = Array.from(document.querySelectorAll('.level-row'));
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
                
                // Numerical sorting for Column 0 (Level) and Column 1 (XP)
                valA = parseInt(valA.replace(/[^0-9]/g, '')) || 0;
                valB = parseInt(valB.replace(/[^0-9]/g, '')) || 0;
                
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
    .icon-glow { filter: drop-shadow(0 0 5px rgba(34, 211, 238, 0.5)); }
    .glass-panel { backdrop-filter: blur(16px); }
</style>
@endsection
