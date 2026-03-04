@extends('layouts.admin')

@section('title', 'Taksonomi Gerbang')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6 animate-fadeIn">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Taksonomi Gerbang</h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
            Klasifikasi Rift & Parameter Kapasitas Hunter
        </p>
    </div>
    
    <div class="flex flex-col md:flex-row items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            @php $currentLimit = request('limit', 16); @endphp
            <div class="flex gap-1" id="row-limit-container">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-type px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-type px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-type px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
            </div>
        </div>

        <a href="{{ route('admin.dungeon-types.create') }}" class="group relative px-8 py-4 rounded-2xl bg-teal-900 text-white shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden font-serif uppercase tracking-widest text-[10px] font-black">
            <span class="relative flex items-center gap-3">
                <i class="fas fa-plus text-cyan-400 icon-glow transition-transform group-hover:rotate-90"></i>
                Inisialisasi Klasifikasi
            </span>
        </a>
    </div>
</div>

<div class="glass-panel p-0 rounded-[32px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
    <div class="overflow-x-auto p-6 pt-5">
        <table class="w-full text-left" id="type-table">
            <thead>
                <tr class="border-b border-slate-100 uppercase">
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">ID Node</th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                        Nama Klasifikasi <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">Kunci Identifikasi</th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(3)">
                        Kapasitas Peserta <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">Instansi Aktif</th>
                    <th class="pb-6 px-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-right">Sinkronisasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 font-sans" id="type-body">
                @forelse($types as $type)
                <tr class="type-row group hover:bg-slate-50/50 transition-colors">
                    <td class="py-6 px-6">
                        <span class="text-[10px] font-black text-slate-300 font-mono tracking-tighter">#DT-0{{ $type->id }}</span>
                    </td>
                    <td class="py-6 px-6">
                        <span class="text-xl font-serif font-black text-teal-900 uppercase tracking-tight group-hover:text-cyan-600 transition-colors duration-300">
                            {{ $type->name }}
                        </span>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-1">Klasifikasi Rift</p>
                    </td>
                    <td class="py-6 px-6">
                        <code class="px-3 py-1 bg-slate-100 rounded-lg text-[10px] font-black text-cyan-600 uppercase tracking-wider border border-slate-200">
                            {{ $type->slug }}
                        </code>
                    </td>
                    <td class="py-6 px-6">
                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-white border-2 border-slate-100 rounded-2xl shadow-sm group-hover:border-teal-200 transition-colors">
                            <i class="fas fa-users text-[10px] text-teal-600"></i>
                            <span class="text-lg font-black text-teal-900 font-mono tracking-tighter">{{ $type->max_participants }}</span>
                            <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Hunters</span>
                        </div>
                    </td>
                    <td class="py-6 px-6">
                        <span class="inline-flex items-center px-4 py-1.5 rounded-xl bg-teal-50 border-2 border-teal-100 text-[9px] font-black text-teal-700 uppercase tracking-widest shadow-sm">
                            <i class="fas fa-portal-exit mr-2 text-[8px] animate-pulse"></i>
                            {{ $type->dungeons_count ?? $type->dungeons()->count() }} Dungeons
                        </span>
                    </td>
                    <td class="py-6 px-6 text-right whitespace-nowrap">
                        <div class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-300">
                            <a href="{{ route('admin.dungeon-types.edit', $type) }}" class="p-4 bg-white border-2 border-slate-100 text-teal-900 hover:text-cyan-500 hover:border-cyan-200 rounded-2xl transition-all shadow-sm active:scale-95 leading-none">
                                <i class="fas fa-sliders text-xs"></i>
                            </a>
                            <form action="{{ route('admin.dungeon-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Apakah anda yakin ingin menghapus klasifikasi ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-4 bg-white border-2 border-slate-100 text-slate-400 hover:text-red-500 hover:border-red-200 rounded-2xl transition-all shadow-sm active:scale-95 leading-none">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-20 text-center text-slate-300 italic font-medium uppercase tracking-[0.2em] text-[10px]">
                        Belum ada klasifikasi gerbang terdeteksi...
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
    <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
        Parameter Taksonomi Gerbang Tersinkronisasi
    </div>
    
    <div class="flex items-center gap-4">
        @if($types instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @if($types->onFirstPage())
                <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $types->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm active:scale-95 font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </a>
            @endif

            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">{{ $types->currentPage() }}</span>
                <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $types->lastPage() }}</span>
            </div>

            @if($types->hasMorePages())
                <a href="{{ $types->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
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

    function sortTable(columnIndex) {
        const tableBody = document.getElementById('type-body');
        const rows = Array.from(document.querySelectorAll('.type-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            // Numerical sorting for participants at column 3
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
    }

    window.onload = () => {};
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
