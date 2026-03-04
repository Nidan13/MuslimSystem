@extends('layouts.admin')

@section('title', 'Arsip Taksonomi Mandat')

@section('content')
<div class="space-y-10">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 animate-fadeIn">
        <div>
            <h2 class="text-4xl font-serif font-black text-teal-900 tracking-wide uppercase">Taksonomi Misi</h2>
            <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
                Definisi Kategori & Protokol Sistem Hunter
            </p>
        </div>
        
        <div class="flex flex-col md:flex-row items-center gap-4">
            <!-- Row Limit Controls -->
            <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
                @php $currentLimit = request('limit', 16); @endphp
                <div class="flex gap-1" id="row-limit-container">
                    <button type="button" onclick="setRowLimit(8)" class="row-btn-type px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                    <button type="button" onclick="setRowLimit(16)" class="row-btn-type px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                    <button type="button" onclick="setRowLimit('all')" class="row-btn-type px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
                </div>
            </div>

            <!-- Add Button -->
            <a href="{{ route('admin.quest-types.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden font-serif uppercase tracking-widest text-[10px] font-black">
                <span class="relative flex items-center gap-3">
                    <i class="fas fa-plus text-cyan-400 icon-glow transition-transform group-hover:rotate-90"></i>
                    Tambah Kategori
                </span>
            </a>
        </div>
    </div>

    <!-- Table Section -->
    <div class="glass-panel p-0 rounded-[40px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-2xl shadow-slate-200/50">
        <div class="overflow-x-auto p-6 pt-5">
            <table class="w-full text-left" id="type-table">
                <thead>
                    <tr class="border-b-2 border-slate-100 uppercase">
                        <th class="pb-6 px-4 text-[10px] font-black text-slate-400 tracking-[0.2em] w-24 cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(0)">
                            <div class="flex items-center gap-2">
                                NODE ID <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity" id="sort-icon-0"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                            <div class="flex items-center gap-2">
                                DESIGNASI TIPE <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity" id="sort-icon-1"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">PARAMETER DESKRIPSI</th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(3)">
                            <div class="flex items-center justify-center gap-2">
                                MISI TERHUBUNG <i class="fas fa-sort opacity-30 group-hover:opacity-100 transition-opacity" id="sort-icon-3"></i>
                            </div>
                        </th>
                        <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-sans" id="type-body">
                    @forelse($types as $type)
                    <tr class="type-row group hover:bg-slate-50/50 transition-colors">
                        <td class="py-6 px-4">
                            <span class="text-[10px] font-black text-slate-300 font-mono tracking-tighter uppercase whitespace-nowrap">#QT-{{ str_pad($type->id, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="py-6 px-6">
                            <div class="flex flex-col">
                                <span class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors duration-300">
                                    {{ $type->name }}
                                </span>
                                <code class="text-[9px] font-black text-cyan-500 tracking-widest mt-1">SLUG: {{ $type->slug }}</code>
                            </div>
                        </td>
                        <td class="py-6 px-6">
                            <p class="text-[11px] font-medium text-slate-400 leading-relaxed max-w-xs italic">
                                {{ $type->description ?? 'Tidak ada parameter deskripsi yang terdefini.' }}
                            </p>
                        </td>
                        <td class="py-6 px-6 text-center">
                            <span class="inline-flex items-center px-4 py-1.5 rounded-xl border-2 border-teal-100 bg-teal-50 text-[9px] font-black text-teal-700 uppercase tracking-widest shadow-sm">
                                {{ $type->quests_count }} MISI AKTIF
                            </span>
                        </td>
                        <td class="py-6 px-6 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                                <a href="{{ route('admin.quest-types.show', $type) }}" class="p-4 bg-white border-2 border-slate-100 text-teal-900 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm active:scale-95 leading-none shadow-slate-200/50">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('admin.quest-types.edit', $type) }}" class="p-4 bg-white border-2 border-slate-100 text-teal-900 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm active:scale-95 leading-none shadow-slate-200/50">
                                    <i class="fas fa-sliders text-xs"></i>
                                </a>
                                <form action="{{ route('admin.quest-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Hapus node taksonomi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-4 bg-white border-2 border-slate-100 text-slate-400 hover:text-red-500 hover:border-red-200 rounded-2xl transition-all shadow-sm active:scale-95 leading-none shadow-slate-200/50">
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
                                <i class="fas fa-folder-open text-slate-100 text-6xl mb-4"></i>
                                <span class="text-slate-300 text-[10px] font-black uppercase tracking-[0.3em]">Taksonomi Belum Didefinisikan</span>
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
            Sinkronisasi Taksonomi Sistem Terverifikasi (Total: {{ $types instanceof \Illuminate\Pagination\LengthAwarePaginator ? $types->total() : $types->count() }})
        </div>
        
        <div class="flex items-center gap-4">
            @if($types instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <!-- Navigation -->
                @if($types->onFirstPage())
                    <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-300 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </span>
                @else
                    <a href="{{ $types->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-xl shadow-slate-200/50 active:scale-95 font-black text-[10px] uppercase tracking-widest">
                        <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                    </a>
                @endif

                <div class="flex items-center gap-1 bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
                    <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-xl shadow-sm border border-slate-100 leading-none flex items-center justify-center min-w-[2.5rem]">
                        {{ $types->currentPage() }}
                    </span>
                    <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $types->lastPage() }}</span>
                </div>

                @if($types->hasMorePages())
                    <a href="{{ $types->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-xl shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
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
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    let sortOrders = { 0: 'asc', 1: 'asc', 3: 'asc' };

    function applyDisplay() {
        // Obsolete
    }

        window.sortTable = function(columnIndex) {
            const tableBody = document.getElementById('type-body');
            const rows = Array.from(document.querySelectorAll('.type-row'));
            const isAsc = sortOrders[columnIndex] === 'asc';

            // Reset and Update Icons
            document.querySelectorAll('th i').forEach(icon => {
                icon.className = 'fas fa-sort opacity-20 transition-opacity';
                icon.style.color = 'inherit';
            });
            const activeIcon = document.getElementById(`sort-icon-${columnIndex}`);
            if (activeIcon) {
                activeIcon.className = `fas fa-sort-${isAsc ? 'up' : 'down'} opacity-100`;
                activeIcon.style.color = '#22d3ee'; // cyan-400
            }

            rows.sort((a, b) => {
                let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
                let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
                
                // Numerical sorting for Column 0 (NODE ID) and Column 3 (LINKED MISSIONS)
                if (columnIndex === 0 || columnIndex === 3) {
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
        };

        // Initialize state
        applyDisplay();
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
