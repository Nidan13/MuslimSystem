@extends('layouts.admin')

@section('title', 'Manajemen Lingkaran')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-6 animate-fadeIn">
    <div>
        <h2 class="text-3xl font-serif font-black text-teal-900 tracking-wide uppercase">Manajemen Lingkaran</h2>
        <p class="text-slate-500 text-[10px] font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse shadow-[0_0_10px_#22d3ee]"></span>
            Manajemen Kolektif Hunter & Ikatan Persaudaraan
        </p>
    </div>
    
    <div class="flex items-center gap-4">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border-2 border-slate-50 shadow-inner">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-4">Baris:</span>
            @php $currentLimit = request('limit', 16); @endphp
            <div class="flex gap-1" id="row-limit-container">
                <button type="button" onclick="setRowLimit(8)" class="row-btn-circle px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 8 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">8</button>
                <button type="button" onclick="setRowLimit(16)" class="row-btn-circle px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 16 ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">16</button>
                <button type="button" onclick="setRowLimit('all')" class="row-btn-circle px-4 py-2 rounded-xl text-[10px] font-black transition-all {{ $currentLimit == 'all' ? 'bg-teal-900 text-white shadow-lg' : 'hover:bg-white hover:text-cyan-500 text-slate-400' }}">Semua</button>
            </div>
        </div>
        <a href="{{ route('admin.circles.create') }}" class="group relative px-6 py-4 rounded-2xl bg-teal-900 text-white font-bold shadow-xl shadow-teal-950/20 hover:bg-teal-800 transition-all active:scale-95 overflow-hidden">
            <span class="relative flex items-center gap-3 tracking-[0.1em] text-xs font-serif uppercase">
                <i class="fas fa-circle-nodes text-cyan-400 icon-glow transition-transform group-hover:scale-110"></i>
                Bentuk Lingkaran
            </span>
        </a>
    </div>
</div>

<div class="glass-panel p-0 rounded-[32px] relative overflow-hidden bg-white border-2 border-slate-50 shadow-xl shadow-slate-200/50">
    <div class="overflow-x-auto p-6 pt-5">
        <table class="w-full text-left">
            <thead>
                <tr class="border-b border-slate-100 uppercase">
                    <th class="pb-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">Lambang</th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 tracking-[0.2em] cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(1)">
                        Nama Lingkaran <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">Info Kolektif</th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 tracking-[0.2em] text-center cursor-pointer hover:text-cyan-500 transition-colors group" onclick="sortTable(3)">
                        Keanggotaan <i class="fas fa-sort ml-1 opacity-50 group-hover:opacity-100"></i>
                    </th>
                    <th class="pb-6 text-[10px] font-black text-slate-400 tracking-[0.2em]">Sinkronisasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 font-sans" id="circle-table-body">
                @forelse($circles as $circle)
                <tr class="circle-row group hover:bg-slate-50/50 transition-colors">
                    <td class="py-6 pr-4">
                        <div class="w-14 h-14 rounded-2xl bg-teal-900 border border-teal-800 flex items-center justify-center text-cyan-200 overflow-hidden shadow-lg shadow-teal-950/20 group-hover:scale-110 transition-transform">
                            @if($circle->icon)
                                <img src="{{ asset('storage/' . $circle->icon) }}" 
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($circle->name) }}&background=134e4a&color=22d3ee';" 
                                     class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-users-rays text-xl opacity-40 group-hover:opacity-100 transition-opacity"></i>
                            @endif
                        </div>
                    </td>
                    <td class="py-6 px-4">
                        <span class="text-sm font-black text-teal-950 uppercase tracking-tight group-hover:text-cyan-600 transition-colors leading-none">
                            {{ $circle->name }}
                        </span>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mt-1">ID: CIR-{{ str_pad($circle->id, 3, '0', STR_PAD_LEFT) }}</p>
                    </td>
                    <td class="py-6 px-4">
                        <p class="text-[11px] text-slate-500 font-medium max-w-xs leading-relaxed italic line-clamp-1">
                            {{ $circle->description ?? 'Belum ada catatan historis untuk lingkaran ini...' }}
                        </p>
                    </td>
                    <td class="py-6 px-4 text-center">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-xl border-2 border-slate-100 bg-white text-teal-900 text-[10px] font-black shadow-sm">
                            <i class="fas fa-id-badge text-[10px] text-cyan-500"></i>
                            {{ $circle->members_count ?? $circle->members()->count() }} Anggota
                        </div>
                    </td>
                    <td class="py-6 text-right">
                        <div class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all translate-x-1 group-hover:translate-x-0">
                            <a href="{{ route('admin.circles.show', $circle) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-teal-900 hover:border-teal-900 transition-all shadow-sm active:scale-95">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.circles.edit', $circle) }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-teal-900 hover:border-cyan-400 hover:text-cyan-600 transition-all shadow-sm active:scale-95 group/btn">
                                <i class="fas fa-sliders group-hover/btn:rotate-12 transition-transform"></i>
                            </a>
                            <form action="{{ route('admin.circles.destroy', $circle) }}" method="POST" class="inline" onsubmit="return confirm('Bubarkan protokol perjanjian ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-red-400 hover:border-red-400 hover:text-red-500 transition-all shadow-sm active:scale-95 group/del">
                                    <i class="fas fa-trash-alt group-hover/del:animate-bounce"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-20 text-center text-slate-300 italic font-medium uppercase tracking-[0.2em] text-[10px]">
                        Belum ada lingkaran terdeteksi di matriks...
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-12 flex flex-col md:flex-row items-center justify-between gap-6 px-1">
    <div class="text-[10px] font-black text-teal-900/30 uppercase tracking-[0.4em]">
        Total Lingkaran Terdeteksi: {{ $circles instanceof \Illuminate\Pagination\LengthAwarePaginator ? $circles->total() : $circles->count() }}
    </div>
    
    <div class="flex items-center gap-4">
        @if($circles instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @if($circles->onFirstPage())
                <span class="px-6 py-3 rounded-2xl bg-slate-50 text-slate-200 border-2 border-slate-100 flex items-center justify-center cursor-not-allowed font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </span>
            @else
                <a href="{{ $circles->previousPageUrl() }}" class="px-6 py-3 rounded-2xl bg-white text-teal-900 border-2 border-slate-100 flex items-center justify-center hover:border-cyan-400 hover:text-cyan-500 transition-all shadow-sm active:scale-95 font-black text-[10px] uppercase tracking-widest">
                    <i class="fas fa-chevron-left mr-2 text-[8px]"></i> Sebelumnya
                </a>
            @endif

            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border-2 border-slate-50 shadow-inner">
                <span class="px-4 py-2 text-[10px] font-black bg-white text-teal-900 rounded-lg shadow-sm border border-slate-100">{{ $circles->currentPage() }}</span>
                <span class="text-[9px] font-black text-slate-400 px-3 uppercase tracking-tighter">DARI {{ $circles->lastPage() }}</span>
            </div>

            @if($circles->hasMorePages())
                <a href="{{ $circles->nextPageUrl() }}" class="px-6 py-3 rounded-2xl bg-teal-900 text-white flex items-center justify-center hover:bg-teal-800 transition-all shadow-lg shadow-teal-950/20 active:scale-95 font-black text-[10px] uppercase tracking-widest">
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
        const tableBody = document.getElementById('circle-table-body');
        const rows = Array.from(document.querySelectorAll('.circle-row'));
        const isAsc = sortOrders[columnIndex] === 'asc';

        rows.sort((a, b) => {
            let valA = a.cells[columnIndex].innerText.trim().toLowerCase();
            let valB = b.cells[columnIndex].innerText.trim().toLowerCase();
            
            // Handle numeric values for membership column
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
